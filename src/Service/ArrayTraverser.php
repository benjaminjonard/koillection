<?php

declare(strict_types=1);

namespace App\Service;

/**
 * This class is ment to make working with arrays and array trees a lot easier.
 * It provides methods to get, set, remove and existence-check paths inside an array tree.
 *
 * A path is by default notated like "key.key.key" or as array: ["key", "key", "key"].
 * But in addition to that simple wildcards like "key.*.key" or "*.key" can be used to
 * make the access more dynamic and intuitive.
 * You can also use key branching like: "key.[key,otherKey].*" to handle your arrays like a pro :D
 */
class ArrayTraverser
{
    /**
     * A list of possible escaped control objects in path's
     */
    const CONTROL_OBJECT_ESCAPING = [
        '\\*' => '*',
        '\*'  => '*',
    ];

    /**
     * The different key types
     */
    const KEY_TYPE_DEFAULT  = 0;
    const KEY_TYPE_WILDCARD = 1;
    const KEY_TYPE_KEYS     = 2;

    /**
     * Contains a list of path's and their array equivalent for faster comparison
     * @var array
     */
    protected $parsedPathCache = [];

    /**
     * Contains a list of subkey key's and the parsed list of keys as array
     * @var array
     */
    protected $parsedKeysByListKey = [];

    /**
     * This method is used to convert a string into a path array.
     * It will also validate already existing path arrays.
     *
     * By default a dot is used to separate path parts like: "my.array.path" => ["my","array","path"].
     * If you require another separator you may either set a new one using the $separator parameter.
     * In most circumstances it will make more sense just to escape a separator. Do so by using the backslash like:
     * "my\.array.path" => ["my.array", "path"].
     *
     * If an array is given it will be validated for invalid parts before returning it again.
     *
     * @param array|string $path      The path to parse as described above.
     * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
     *
     * @return array
     * @throws \InvalidArgumentException If the validation of path or it's parts fails
     */
    public function parsePath($path, string $separator = '.'): array
    {
        // Check if the input is empty
        if (empty($path)) return [];

        // Validate input
        if (!is_string($path) && !is_numeric($path) && !is_array($path))
        {
            throw new \InvalidArgumentException('The given path: ' . serialize($path) . ' is not valid! Only strings, numerics and arrays are supported!');
        }

        // Check if we know this path already
        $cacheKey = md5(is_string($path) || is_numeric($path) ? $path : json_encode($path) . $separator);
        if (isset($this->parsedPathCache[$cacheKey]))
        {
            return $this->parsedPathCache[$cacheKey];
        }

        // Check if the given path array is valid
        if (is_array($path))
        {
            // Validate the input
            $path = array_values($path);
            $validPathParts = array_filter($path, function ($v) {
                // Filter out all non strings and non numerics
                return is_string($v) || is_numeric($v);
            });

            // Check if all path parts are valid
            if (count($path) !== count($validPathParts))
            {
                throw new \InvalidArgumentException('Not all parts of the given path are formatted correctly. ' .
                    'There are problems with: ' . implode(', ', array_diff($validPathParts, $path)));
            }

        } else
        {
            // Parse the path from a string
            $hasEscapedSeparator = stripos($path, '\\' . $separator) !== false;
            $path = array_map('trim',
                preg_split('~(?<!\\\)' . preg_quote($separator, '~') . '~', $path, -1, PREG_SPLIT_NO_EMPTY)
            );

            // Remove escaped separators in created path keys
            if ($hasEscapedSeparator)
            {
                $path = array_map(function ($v) use ($separator) {
                    return str_replace(['\\' . $separator], $separator, $v);
                }, $path);
            }
        }

        // Store te cache
        $this->parsedPathCache[$cacheKey] = $path;

        // Done
        return $path;
    }

    /**
     * This method can be used to merge two path segments together.
     * This becomes useful if you want to work with a dynamic part in form of an array
     * and a static string part. The result will always be a path array.
     * You can specify a separator type for each part of the given path if you merge
     * differently formatted paths.
     *
     * It merges stuff like:
     *        - "a.path.to." and ["parts","inTheTree"] => ["a", "path", "to", "parts", "inTheTree"]
     *        - "a.b.*" and "c.d.[asdf|id]" => ["a", "b", "*", "c", "d", "[asdf|id]"
     *        - "a.b" and "c,d" => ["a","b","c","d"] (If $separatorB is set to ",")
     * and so on...
     *
     * @param array|string $pathA      The path to add $pathB to
     * @param array|string $pathB      The path to be added to $pathA
     * @param string       $separatorA The separator for string paths in $pathA
     * @param string       $separatorB The separator for string paths in $pathB
     *
     * @return array
     */
    public function mergePaths($pathA, $pathB, $separatorA = '.', $separatorB = '.'): array
    {
        $pathA = $this->parsePath($pathA, $separatorA);
        $pathB = $this->parsePath($pathB, $separatorB);
        foreach ($pathB as $p)
        {
            $pathA[] = $p;
        }
        return $pathA;
    }

    /**
     * This is a multi purpose tool to handle different scenarios when dealing with array lists.
     * The best option to describe it, is to show some examples in this case.
     * We assume an input array like:
     * $array = [
     *        [
     *            'id' => '234',
     *            'title' => 'medium',
     *            'asdf' => 'asdf',
     *            'array' => [
     *                    'id' => '12',
     *                    'rumpel' => 'di',
     *                    'bar' => 'baz',
     *                ]
     *        ],
     *        [
     *            'id' => '123',
     *            'title' => 'apfel',
     *            'asdf' => 'asdf',
     *            'array' => [
     *                    'id' => '23',
     *                    'rumpel' => 'pumpel',
     *                    'foo' => 'bar'
     *                ]
     *        ]
     * ];
     *
     * // Example 1: Return a list of all "id" values
     * getList($array, ['id']);
     * Result: ['234','123'];
     *
     * // Example 2: Return a list of all "id" and "title" values
     * getList($array, ['id', 'title']);
     * Result: [
     *           ['id' => '234', 'title' => 'medium'],
     *           [ 'id' => '123', 'title' => 'apfel']
     *        ];
     *
     * // Example 3: Return a list of all "title" values by their matching "id" value
     * getList($array, ['title'], 'id');
     * Result: ['234' => 'medium', '123' => 'apfel'];
     *
     * // Example 4: Subarrays, aliases and default values for missing values
     * getList($array, ['array.id', 'array.bar as foo'], 'id');
     * Result: [
     *            '234' => ['array.id' => '12', 'foo' => 'baz'],
     *            '123' => ['array.id' => '23', 'foo' => null]
     *        ];
     *
     * // Example 5: Extracting columns as keys
     * getList($array, [], 'id');
     * Result: ['234' => [VALUE UNCHANGED], '123' => [VALUE UNCHAGED]];
     *
     * // Example 6: Sorting entries by a key
     * getList($array, [], 'asdf', '*', null, '.', TRUE);
     * Result: ['asdf' => [ [VALUE UNCHANGED], [VALUE UNCHANGED] ];
     *
     * // Example 7: Dealing with strange sorting and nested lists
     * // We assume for this example: $array = [ 'foo' => $array ];
     * getList($array, ['id'], '', 'foo.*');
     * Result: ['234','123'];
     *
     * // Example 8: Dealing with path based key keys
     * getList($array, ['id'], 'array.id');
     * Result: ['12' => '234', '23' => '123'];
     *
     * @param array      $input       The input array to gather the list from. Should be a list of arrays.
     * @param array      $valueKeys   The list of value keys to extract from the list, can contain sub-paths
     *                                like seen in example 4
     * @param string     $keyKey      Optional key or sub-path which will be used as key in the result array
     * @param string     $path        Optional path to filter / normalize the $input array. Default: '*' => array list
     * @param null|mixed $default     The default value if a key was not found in $input.
     * @param string     $separator   A separator which is used when splitting string paths
     * @param bool       $gatherLists True to gather lists by keys instead of overwriting already set keys.
     *
     * @return array|null
     * @throws \InvalidArgumentException if the given path is empty
     */
    public function &getList(&$input, array $valueKeys, string $keyKey = '', $path = '*',
                             $default = null, string $separator = '.', bool $gatherLists = false)
    {
        // Ignore empty inputs
        if (!is_array($input)) return $default;

        // Convert the path into an array
        $path = $this->parsePath($path, $separator);

        // Ignore empty paths
        if (empty($path)) throw new \InvalidArgumentException('The given path was empty!');

        // Check if we have to dig deeper -> $path !== '*'
        if ($path !== ['*'])
        {
            // Resolve the root path using get
            $input = $this->get($input, $path, $default, $separator);
        }

        // Validate if the input is still an array list
        if (empty($input) || count(array_filter($input, function ($v) { return is_array($v); })) !== count($input))
        {
            return $default;
        }

        // Holds the output
        $result = [];
        // True if only a single valueKey was requested => unwrap the output so a key => value array is returned
        $isSingleValueKey = count($valueKeys) === 1;
        // True if a $keyKey was defined
        $hasKeyKey = !empty($keyKey);
        // True if a $keyKey was defined, but not requested in the list of $valueKeys => we will have to remove it at the end
        $keyKeyWasInjected = false;
        // Marks an empty result when resolving path value fields
        $emptyMarker = '__EMPTY__' . rand(0, 999) . '__';

        // Fastlane for empty value fields
        if (!$isSingleValueKey && empty($valueKeys))
        {

            // Special handling if everything stays, but the parent key should be set
            if ($hasKeyKey)
            {
                // Check if we can use the fast lookup
                $simpleKey = stripos($keyKey, $separator) === false;
                foreach ($input as $row)
                {
                    // Get the key value
                    if ($simpleKey)
                    {
                        $keyKeyValue = isset($row[$keyKey]) ? $row[$keyKey] : null;
                    } else
                    {
                        $keyKeyValue = $this->get($row, $keyKey, null, $separator);
                    }

                    // Build the output
                    if ($keyKeyValue === null)
                    {
                        // Sequential
                        $result[] = $row;
                    } else
                    {
                        // Associative
                        if ($gatherLists)
                        {
                            $result[$keyKeyValue][] = $row;
                        } else
                        {
                            $result[$keyKeyValue] = $row;
                        }
                    }
                }

                // Done
                return $result;
            }

            // Return the processed input value
            return $input;
        }

        // Add key key to the list of required keys
        if ($hasKeyKey && !in_array($keyKey, $valueKeys))
        {
            $valueKeys[] = $keyKey;
            $keyKeyWasInjected = true;
        }

        // This block checks if we have to resolve keys which are sub-paths in the current array list.
        // It is possible to define a valueKey like sub.array.id to extract that deeper level's
        // information and put it into the current context. The key will be the same as the
        // path, in our case: "sub.array.id", if we want something more speaking we can
        // define an alias like sub.array.id as myId. Now the value will show up with myId as key.
        // This block prepares the parsing so we don't have to do it in every loop
        $pathValueKeys = $simpleValueKeys = $keyAliasMap = [];
        array_map(function ($v) use ($separator, &$pathValueKeys, &$simpleValueKeys, &$keyAliasMap, $isSingleValueKey) {
            // Store the alias
            $vOrg = $alias = $v;
            $aliasSeparator = ' as ';
            if (stripos($v, $separator) !== false)
            {
                // Check for an alias || Ignore when only one value will be returned -> save performance (a bit at least)
                if (!$isSingleValueKey && stripos($v, $aliasSeparator) !== false)
                {
                    $v = explode($aliasSeparator, $v);
                    $alias = array_pop($v);
                    $v = implode($aliasSeparator, $v);
                }
                $pathValueKeys[$alias] = $v;
                $simpleValueKeys[] = $alias;
            } else
            {
                $simpleValueKeys[] = $v;
            }
            $keyAliasMap[$vOrg] = $alias;
        }, $valueKeys);
        $hasPathValueKeys = !empty($pathValueKeys);
        $simpleValueKeys = array_fill_keys($simpleValueKeys, $default);

        // Loop over the list of rows
        foreach ($input as $row)
        {
            // Only simple value keys -> use the fast lane
            $rowValues = array_intersect_key($row, $simpleValueKeys);

            // Determine if the value keys contain paths themselfs
            if ($hasPathValueKeys)
            {
                // Contains path value keys -> also gather their values
                foreach ($pathValueKeys as $alias => $pathValueKey)
                {
                    // Read the path value from the current context
                    $value = $this->get($row, $pathValueKey, $emptyMarker, $separator);
                    if ($value !== $emptyMarker)
                    {
                        $rowValues[$alias] = $value;
                    }
                }
            }

            // Check if we are completely empty
            if (empty($rowValues)) continue;

            // Get key key
            $keyKeyValue = $keyKeyWasInjected ? $rowValues[$keyAliasMap[$keyKey]] : null;

            // Remove if the key key was injected and not part of the requested columns
            if ($keyKeyWasInjected)
            {
                unset($rowValues[$keyAliasMap[$keyKey]]);
            }

            // Check if we have a single value key -> strip the surrounding array
            if ($isSingleValueKey)
            {
                $rowValues = reset($rowValues);
            } else
            {
                // Fill up with default values (if we are missing some)
                $rowValues = array_merge($simpleValueKeys, $rowValues);
            }

            // Append to result
            if ($hasKeyKey && !empty($keyKeyValue))
            {
                // Associative
                if ($gatherLists)
                {
                    $result[$keyKeyValue][] = $rowValues;
                } else
                {
                    $result[$keyKeyValue] = $rowValues;
                }
            } else
            {
                // Sequential
                $result[] = $rowValues;
            }
        }

        // Done
        return $result;
    }

    /**
     * This method checks if a given path exists in a given $input array
     *
     * @param array|mixed  $input     The array to check
     * @param array|string $path      The path to check for in $input
     * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
     *
     * @return bool
     * @throws \InvalidArgumentException if the given path is empty
     */
    public function has($input, $path, string $separator): bool
    {
        // Ignore empty inputs
        if (!is_array($input)) return false;

        // Fastlane for simple paths
        if (is_string($path) && stripos($path, $separator) === false)
        {
            return array_key_exists($path, $input);
        }

        // Convert the path into an array
        $path = $this->parsePath($path, $separator);

        // Ignore empty paths
        if (empty($path)) throw new \InvalidArgumentException('The given path was empty!');

        // Handle walker checker
        try
        {
            // If this does not throw an exception -> all paths were found
            $this->hasWalker($input, $path);
            return true;
        } catch (\Exception $e)
        {
            return false;
        }
    }

    /**
     * This method reads a single value or multiple values (depending on the given $path) from
     * the given $input array.
     *
     * All results will be returned as references to the original input, so you can
     * use them as pointers to edit the values in a huge array tree.
     *
     * @param array|mixed  $input     The array to retrieve the path's values from
     * @param array|string $path      The path to retreive from the $input array
     * @param null         $default   The value which will be returned if the $path did not match anything.
     * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
     *
     * @return array|mixed|null
     * @throws \InvalidArgumentException if the given path is empty
     */
    public function &get(&$input, $path, $default = null, string $separator = '.')
    {
        // Ignore empty inputs
        if (!is_array($input)) return $default;

        // Fastlane for simple paths
        if (is_string($path) && stripos($path, $separator) === false && array_key_exists($path, $input))
        {
            return $input[$path];
        }

        // Convert the path into an array
        $path = $this->parsePath($path, $separator);

        // Ignore empty paths
        if (empty($path)) throw new \InvalidArgumentException('The given path was empty!');

        // Extract the result object
        $result = &$this->getWalker($input, $path, $default);

        return $result;
    }

    /**
     * This method lets you set a given value to a certain path of your array.
     * You can also set multiple keys to the same value at once if you use wildcards.
     *
     * @param array|mixed  $input     The array to set the values in
     * @param array|string $path      The path to set $value at
     * @param mixed        $value     The value to set at $path in $input
     * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
     *
     * @return void
     * @throws \InvalidArgumentException if the given path is empty
     */
    public function set(&$input, $path, $value, string $separator = '.')
    {
        // Fastlane for simple paths
        if (is_string($path) && stripos($path, $separator) === false)
        {
            $input[$path] = $value;
            return;
        }

        // Convert the path into an array
        $path = $this->parsePath($path, $separator);

        // Ignore empty paths
        if (empty($path)) throw new \InvalidArgumentException('The given path was empty!');

        // Make sure $input is an array
        if(!is_array($input)){
            $input = [];
        }

        // Start the walker
        $this->setWalker($input, $path, $value);
    }

    /**
     * Removes the values at the given $path from the $input array.
     * It can also remove multiple values at once if you use wildcards.
     *
     * NOTE: The method tries to remove empty remains recursively when the last
     * child was removed from the branch. If you don't want to use this behaviour
     * set $removeEmptyRemains to false.
     *
     * @param array|mixed  $input              The array to remove the values from
     * @param array|string $path               The path which defines which values have to be removed
     * @param string       $separator          Default: "." Can be set to any string you want to use as separator of
     *                                         path parts.
     * @param bool         $removeEmptyRemains Set this to false to disable the automatic cleanup of empty remains when
     *                                         the lowest child was removed from a tree.
     *
     * @return void
     * @throws \InvalidArgumentException if the given path is empty
     */
    public function remove(&$input, $path, string $separator = '.', bool $removeEmptyRemains = true)
    {
        // Ignore empty inputs
        if (!is_array($input)) return;

        // Fastlane for simple paths
        if (is_string($path) && stripos($path, $separator) === false)
        {
            unset($input[$path]);
            return;
        }

        // Convert the path into an array
        $path = $this->parsePath($path, $separator);

        // Start walker
        $this->removeWalker($input, $path, $removeEmptyRemains);
    }

    /**
     * This method can be used to apply a filter to all values the given $path matches to.
     * The given $callback will receive the following parameters:
     * $path: 'the.path.trough.your.array' to let you decide how to handle the current value
     * $value: The reference of the current $input's value. Change this value to change $input corrospondingly.
     * The callback should always return void.
     *
     * @param array|mixed  $input     The array to filter
     * @param array|string $path      The path which defines the values to filter
     * @param callable     $callback  The callback to trigger on every value found by $path
     * @param string       $separator Default: "." Can be set to any string you want to use as separator of path parts.
     *
     * @return void
     * @throws \InvalidArgumentException if the given path is empty
     */
    public function filter(&$input, $path, callable $callback, string $separator = '.')
    {
        // Ignore empty inputs
        if (!is_array($input)) return;

        // Convert the path into an array
        $path = $this->parsePath($path, $separator);

        // Ignore empty paths
        if (empty($path)) throw new \InvalidArgumentException('The given path was empty!');

        // Start the walker
        $this->filterWalker($input, $path, $callback, $separator);
    }

    /**
     * The recursive logic of filter();
     *
     * @internal
     *
     * @param          $input
     * @param array    $path
     * @param callable $callback
     * @param string   $separator
     * @param string   $pathCallback
     */
    protected function filterWalker(array &$input, array $path, callable $callback, string $separator, string $pathCallback = '')
    {
        // Initialize the walker
        list($keys, $isLastKey, $keyType) = $this->inititializeWalker($input, $path);

        // Loop over all keys to remove
        foreach ($keys as $key)
        {
            // Prepare local path
            $pathCallbackLocal = ltrim($pathCallback . $separator . $key, $separator);

            // Execute filter on last key
            if ($isLastKey)
            {
                call_user_func_array($callback, [$pathCallbackLocal, &$input[$key]]);
            } else
            {
                // Remove children
                if (isset($input[$key]) && is_array($input[$key]))
                {
                    $this->filterWalker($input[$key], $path, $callback, $separator, $pathCallbackLocal);
                }
            }
        }
    }

    /**
     * The recursive logic of remove();
     *
     * @internal
     *
     * @param       $input
     * @param array $path
     * @param bool  $removeEmptyRemains
     */
    protected function removeWalker(array &$input, array $path, bool $removeEmptyRemains)
    {
        // Initialize the walker
        list($keys, $isLastKey, $keyType) = $this->inititializeWalker($input, $path);

        // Loop over all keys to remove
        foreach ($keys as $key)
        {
            // Remove the value if we reached the last key
            if ($isLastKey)
            {
                unset($input[$key]);
            } else
            {
                // Remove children
                if (isset($input[$key]) && is_array($input[$key]))
                {
                    $this->removeWalker($input[$key], $path, $removeEmptyRemains);
                }
                // Clean up if input is empty now
                if ($removeEmptyRemains && empty($input[$key]))
                {
                    unset($input[$key]);
                }
            }
        }
    }

    /**
     * The recursive logic of set();
     *
     * @internal
     *
     * @param       $input
     * @param array $path
     * @param       $value
     */
    protected function setWalker(array &$input, array $path, $value)
    {
        // Initialize the walker
        list($keys, $isLastKey, $keyType) = $this->inititializeWalker($input, $path);

        // Loop over all required keys
        foreach ($keys as $key)
        {
            // Set the value when we reached the last key
            if ($isLastKey)
            {
                $input[$key] = $value;
                continue;
            }

            // Create subarray if required
            if (!array_key_exists($key, $input) || !is_array($input[$key]))
            {
                $input[$key] = [];
            }

            // Go down the rabbit hole
            $this->setWalker($input[$key], $path, $value);
        }
    }

    /**
     * The recursive logic of has();
     *
     * @internal
     *
     * @param array $input
     * @param array $path
     *
     * @throws \Exception
     */
    protected function hasWalker(array $input, array $path)
    {
        // Initialize the walker
        list($keys, $isLastKey, $keyType) = $this->inititializeWalker($input, $path);

        // Empty inputs
        if (empty($input) || empty($keys)) throw new \Exception('Empty input');

        // Loop over all required keys
        foreach ($keys as $key)
        {

            // Does this part exist?
            if (!array_key_exists($key, $input))
            {
                throw new \Exception($key);
            }

            // Is this part the last part
            if (!$isLastKey)
            {
                // Check for an array
                if (is_array($input[$key]))
                {
                    $this->hasWalker($input[$key], $path);
                    continue;
                }
                // Non array and not the last part -> nope
                throw new \Exception($key);
            }
        }
    }

    /**
     * The recursive logic of get()
     *
     * @internal
     *
     * @param array $input
     * @param array $path
     * @param       $default
     *
     * @return array|mixed
     */
    protected function &getWalker(array &$input, array $path, $default)
    {

        // Initialize the walker
        list($keys, $isLastKey, $keyType) = $this->inititializeWalker($input, $path);

        // Empty inputs
        if (empty($input) || empty($keys)) return $default;

        // Prepare result
        $result = [];

        // Loop over all required keys
        foreach ($keys as $key)
        {

            // Does this part exist?
            if (!array_key_exists($key, $input))
            {
                $result[$key] = $default;
                continue;
            }

            // Is this part the last part
            if (!$isLastKey)
            {
                // Check for an array
                if (is_array($input[$key]))
                {
                    $result[$key] = &$this->getWalker($input[$key], $path, $default);
                    continue;
                }
                // Non array and not the last part -> nope
                $result[$key] = $default;
                continue;
            }

            // Last part and exists -> Store result
            $result[$key] = &$input[$key];
        }

        // Convert result if we dynamically wrapped the default type.
        if ($keyType === static::KEY_TYPE_DEFAULT)
        {
            $result = &$result[key($result)];
        }
        return $result;
    }

    /**
     * This helper is used by all walkers to prepare the global overhead
     * of "unescaping" keys, exploding sub-keys and so on.
     *
     * @internal
     *
     * @param array  $input
     * @param array  $path
     * @param string $keySeparator
     *
     * @return array
     */
    protected function inititializeWalker(array $input, array &$path, string $keySeparator = ','): array
    {
        // Get the current key we have to work with
        $key = $keyEscaped = (string)array_shift($path);
        $isLastKey = empty($path);

        // Handle control object escaping
        if (isset(static::CONTROL_OBJECT_ESCAPING[$keyEscaped]))
        {
            $key = static::CONTROL_OBJECT_ESCAPING[$keyEscaped];
        }

        // Get the type of the current key
        if ($keyEscaped === '*')
        {
            // WILDCARD
            $keyType = static::KEY_TYPE_WILDCARD;

            // Gather the keys to work with
            $keys = array_keys($input);
        } else if (isset($keyEscaped[0]) && $keyEscaped[0] === '[' && substr($keyEscaped, -1) === ']')
        {
            // SUBKEYLIST
            $keyType = static::KEY_TYPE_KEYS;

            // Gather the keys to work with
            if (isset($this->parsedKeysByListKey[$keyEscaped]))
            {
                // Load from cache
                $keys = $this->parsedKeysByListKey[$keyEscaped];
            } else
            {
                // Parse the columns of the given part type
                $tmp = substr(trim($keyEscaped), 1, -1);
                $keys = array_map('trim',
                    preg_split('~(?<!\\\)' . preg_quote($keySeparator, '~') . '~', $tmp, -1, PREG_SPLIT_NO_EMPTY)
                );
                // Store for later use
                $this->parsedKeysByListKey[$keyEscaped] = $keys;
            }
        } else
        {
            // DEFAULT
            $keyType = static::KEY_TYPE_DEFAULT;

            // Gather the keys to work with
            $keys = [$key];
        }

        // Done
        return [$keys, $isLastKey, $keyType];
    }
}

