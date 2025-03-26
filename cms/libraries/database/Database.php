<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Database\Adapter\AdapterInterface;
use Junco\Database\Adapter\StatementInterface;
use Junco\Database\Adapter\ResultInterface;
use Junco\Database\Schema\Interface\SchemaInterface;

/**
 * Database
 * 			
 * @require <Language>, <Router>
 */
class Database
{
    // vars
    protected $adapter            = null;
    protected $profiler            = false;
    protected $prefix            = '';
    protected $last_query        = '';
    protected $queries            = [];
    //
    protected $params            = [];
    protected $where            = [];
    protected $having            = [];
    protected $order            = '';
    protected $sort                = '';
    // pagination
    public    $cur_page            = 0;
    public    $rows_per_page    = 0;

    // constants
    const FETCH_ASSOC            = MYSQLI_ASSOC;
    const FETCH_NUM                = MYSQLI_NUM;
    const FETCH_COLUMN            = -1;
    //const FETCH_CLASS			= -2;
    //
    protected $schema            = null;
    protected $prefixer            = null;

    /**
     * Constructor
     */
    public function __construct(array $config = [])
    {
        $config    = array_merge(config('database'), $config);

        $this->profiler    = config('system.profiler');
        $this->adapter  = $this->getAdapter($config);
        $this->prefix   = $config['database.prefix'];

        if ($config['database.timezone']) {
            $tz = (new DateTime)->format('P');
            $this->query("SET time_zone = '$tz'");
        }
    }

    /**
     * Get
     */
    public function getAdapter(array $config): AdapterInterface
    {
        switch ($config['database.adapter']) {
            case 'pdo':
                $config['database.adapter'] = config('database-pdo.adapter');
                return new Junco\Database\Adapter\PdoAdapter($config);

            case 'pgsql':
                //return new Junco\Database\Adapter\PgsqlAdapter($config);

            case 'mock':
                return new Junco\Database\Adapter\MockAdapter($config);

            default:
                return new Junco\Database\Adapter\MysqlAdapter($config);
        }
    }

    /**
     * Checks if a DB connection is active
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->adapter->isConnected();
    }

    /**
     * Set Param
     *
     * @param mixed  $param
     */
    public function setParam($param): void
    {
        $this->params[] = $param;
    }

    /**
     * Where
     *
     * @param string  $where
     * @param mixed   $params
     */
    public function where(string $where, ...$params): void
    {
        $where = explode('|', $where);

        if (count($where) > 1) {
            if ($params) {
                foreach ($where as $i => $clause) {
                    if (isset($params[$i])) {
                        $offset = $i;
                    }
                    $this->params[] = $params[$offset];
                }
            }
            $this->where[] = '(' . implode(' OR ', $where) . ')';
        } else {
            foreach ($params as $param) {
                $this->params[] = $param;
            }
            $this->where[] = $where[0];
        }
    }

    /**
     * Having
     *
     * @param string  $having
     * @param mixed   $params
     */
    public function having(string $having, ...$params): void
    {
        foreach ($params as $param) {
            $this->params[] = $param;
        }
        $this->having[] = $having;
    }

    /**
     * Order
     *
     * @param int  $index
     * @param int  $orders
     * @param int  $default
     */
    public function order(int &$index, array $orders, int $default = 0): void
    {
        if (!isset($orders[$index])) {
            $index = $default;

            if (!isset($orders[$index])) {
                throw new Error('The "order" statement has an error in the default value');
            }
        }
        $this->order = $orders[$index];
    }

    /**
     * Sort
     *
     * @param string $sort
     * @param string $default
     */
    public function sort(string &$sort, string $default = 'asc'): void
    {
        if ($sort == 'desc') {
            $this->sort = 'DESC';
        } elseif ($sort == 'asc') {
            $this->sort = 'ASC';
        } elseif ($default == 'desc') {
            $sort = 'desc';
            $this->sort = 'DESC';
        } else {
            $sort = 'asc';
            $this->sort = 'ASC';
        }
    }

    /**
     * Executes a prepared query and stores the result
     *
     * @param StatementInterface|string	$stmt    The StatementInterface object or a string with the query.
     * @param array						$params  An array with the values to pass.
     *
     * @return ResultInterface
     */
    public function safeFind(StatementInterface|string $stmt, ...$params): ResultInterface
    {
        $this->transformParam($params);

        if (is_string($stmt)) {
            $query = $this->getQuery($stmt, $params);

            if (!$params) {
                return $this->query($query);
            }
            $stmt = $this->prepare($query);
        }
        $stmt->execute($params);

        return $stmt->getResult();
    }

    /**
     * Paginated query
     *
     * @param string $query
     * @param array  $params
     * 
     * @return Pagination
     */
    public function paginate(string $query, ...$params): Pagination
    {
        $this->transformParam($params);
        $query = $this->getQuery($query, $params);

        // pagination
        $pagi            = new Pagination();
        $pagi->num_rows = $this->countRows($query, $params);

        // calculate
        if ($this->cur_page) {
            $pagi->cur_page = $this->cur_page;
            $this->cur_page = 0;
        }
        if ($this->rows_per_page) {
            $pagi->rows_per_page = $this->rows_per_page;
            $this->rows_per_page = 0;
        }
        $pagi->calculate();

        // query - slice rows
        if ($pagi->num_rows) {
            $query = preg_replace_callback('#\[([^\]]*)\](\*)?+#m', function ($match) {
                return $match[1];
            }, $query) . " LIMIT {$pagi->offset}, {$pagi->rows_per_page}";

            $pagi->setRows($this->simpleQuery($query, $params)->fetchAll());
        }

        return $pagi;
    }

    /**
     * Executes a prepared query
     *
     * @param StatementInterface|string	$stmt    The StatementInterface object or a string with the query.
     * @param array					        $params  An array with the values to pass.
     *
     * @return int
     */
    public function safeExec(StatementInterface|string $stmt, ...$params): int
    {
        $this->transformParam($params);

        if (is_string($stmt)) {
            $query = $this->getQuery($stmt, $params);

            if (!$params) {
                return $this->exec($query);
            }
            $stmt = $this->prepare($query);
        }
        $stmt->execute($params);

        return $stmt->countAffected();
    }

    /**
     * Executes a prepared query
     *
     * @param StatementInterface|string $stmt     The StatementInterface object or a string with the query.
     * @param array                       $_params  An array with the values to pass.
     */
    public function safeExecAll(StatementInterface|string $stmt, ...$_params): void
    {
        if (is_string($stmt)) {
            $query = $this->getQuery($stmt, $_params, true);
            $stmt = $this->prepare($query);
        }
        foreach ($_params as $params) {
            $stmt->execute($params);
        }
    }

    /**
     * Transforms the parameters passed as a single array. 
     *
     * @param array &$params
     *
     * @return void
     */
    protected function transformParam(&$params): void
    {
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }
    }

    /**
     * Get
     *
     * @param string   $query       The query string.
     * @param array    $params
     *
     * @return string
     */
    protected function getQuery(string $query, &$params, bool $is_multiple = false): string
    {
        if (
            false !== strpos($query, '[WHERE]')
            || false !== strpos($query, '[HAVING]')
        ) {
            $params = $this->params;
            $this->params = [];

            $replaces = [];
            if ($this->where) {
                $replaces['[WHERE]'] = 'WHERE ' . implode(' AND ', $this->where);
                $this->where = [];
            } else {
                $replaces['[WHERE]'] = '';
            }
            if ($this->having) {
                $replaces['[HAVING]'] = 'HAVING ' . implode(' AND ', $this->having);
                $this->having = [];
            } else {
                $replaces['[HAVING]'] = '';
            }

            $query = strtr($query, $replaces);
        }

        if (false !== strpos($query, '[ORDER]')) {
            if ($this->order) {
                if ($this->sort && false === strpos($this->order, '[SORT]')) {
                    $this->order .= ' ' . $this->sort;
                    $this->sort = '';
                }
                $this->order = 'ORDER BY ' . $this->order;
            }
            $query = str_replace('[ORDER]', $this->order, $query);
            $this->order = '';
        } elseif ($this->order) {
            throw new Error('There is an "order" statement without its placeholder');
        }

        if (false !== strpos($query, '[SORT]')) {
            $query = str_replace('[SORT]', $this->sort, $query);
            $this->sort = '';
        } elseif ($this->sort) {
            throw new Error('There is an "sort" statement without its placeholder');
        }

        if (
            false !== strpos($query, '??')
            || false !== strpos($query, '?..')
            || false !== strpos($query, '%?')
        ) {
            if (!$params) {
                throw new ArgumentCountError('The number of variables must match the number of parameters in the prepared statement');
            }
            $query = $this->replacePlaceholders($query, $params, $is_multiple);
        }

        return $query;
    }

    /**
     * Replace
     *
     * @param string   $query       The query string.
     * @param array    $params
     *
     * @return string
     */
    protected function replacePlaceholders(string $query, &$params, bool $is_multiple = false): string
    {
        $placeholder = [
            '%?' => [],
            'replaces' => [],
        ];
        $num_holders = substr_count($query, '??');
        if ($num_holders) {
            if ($num_holders > 3) {
                throw new Error('There can be no more than 3 placeholders in the query');
            }
            $placeholder['??'] = [
                'process_on'    => ($num_holders == 1 ? 1 : 2),
                'count'            => 0,
            ];
        }
        $offset = -1;
        $query = preg_replace_callback('#\?\?|\?\.\.?|%\?|\?#', function ($match) use (&$offset, &$placeholder) {
            $pattern = $match[0];
            ++$offset;

            if ($pattern == '?') {
                return '?';
            }
            if ($pattern == '%?') {
                $placeholder['%?'][] = $offset;
                return '?';
            }
            if ($pattern == '??') {
                ++$placeholder['??']['count'];
                $key = $placeholder['??']['count'] . '??';

                if ($placeholder['??']['process_on'] != $placeholder['??']['count']) {
                    --$offset;
                    return $key;
                }
                $placeholder['??']['offset'] = $offset;
            } else {
                $key = $offset . $pattern;
            }

            $placeholder['replaces'][$key] = [
                'offset' => $offset,
                'pattern' => $pattern
            ];

            return $key;
        }, $query);

        // normalize params
        if ($is_multiple) {
            if ($num_holders) {
                $offset = $placeholder['??']['offset'];
                $total  = count($params[$offset]);
            } else {
                throw new ArgumentCountError('The number of variables must match the number of parameters in the prepared statement');
            }

            $placeholders = array_column($placeholder['replaces'], 'offset');

            foreach ($params as $index => $value) {
                if ($index != $offset) {
                    if (!is_array($value) || in_array($index, $placeholders)) {
                        $value = array_fill(0, $total, $value);
                    } elseif (count($value) != $total) {
                        throw new ArgumentCountError('The number of variables must match the number of parameters in the prepared statement');
                    }
                }
                $params[$index] = $value;
            }

            $normal    = [];
            for ($i = 0; $i < $total; $i++) {
                foreach ($params as $value) {
                    $normal[$i][] = $value[$i];
                }
            }
            $params = $normal;
        } else {
            foreach ($placeholder['replaces'] as $key => $row) {
                if ($row['pattern'] == '?..') {
                    if (!isset($params[$row['offset']])) {
                        throw new ArgumentCountError('The number of variables must match the number of parameters in the prepared statement');
                    } elseif (!is_array($params[$row['offset']])) {
                        if ($row['offset'] == 0) {
                            $params = [$params];
                        } else {
                            $params[$row['offset']] = [$params[$row['offset']]];
                        }
                    }
                } else {
                    if ($row['offset'] == 0 && is_string(array_key_first($params))) {
                        // the user entered placeholder in simple array.
                        $params = [$params];
                    } elseif (!isset($params[$row['offset']])) {
                        throw new ArgumentCountError('The number of variables must match the number of parameters in the prepared statement');
                    } elseif (!is_array($params[$row['offset']])) {
                        // convert scalar in array.
                        $params[$row['offset']] = [$params[$row['offset']]];
                    }
                }
            }

            $params = [$params];
        }

        // replaces
        if ($placeholder['%?']) {
            foreach ($placeholder['%?'] as $offset) {
                foreach ($params as $i => $p) {
                    $params[$i][$offset] = (strlen($params[$i][$offset]) == 1 ? '' : '%') . $params[$i][$offset] . '%';
                }
            }
        }

        $corrector = 0;
        foreach ($placeholder['replaces'] as $key => $row) {
            $offset = $row['offset'] + $corrector;
            $total = count($params[0][$offset]);

            if ($row['pattern'] == '?..') {
                $replace = implode(', ', array_fill(0, $total, '?'));
            } else {
                $fields = array_keys($params[0][$offset]);

                switch ($num_holders) {
                    case 1: // Is update
                        $replace = '`' . implode('` = ?, `', $fields) . '` = ?';
                        break;

                    case 3: // Is insert + update
                        foreach ($params as $i => $p) {
                            foreach ($params[$i][$offset] as $value) {
                                $params[$i][] = $value;
                            }
                        }
                        $placeholder['replaces']['3??'] = '`' . implode('` = ?, `', $fields) . '` = ?';
                        // break;

                    case 2: // Is insert
                        $placeholder['replaces']['1??'] = '`' . implode('`, `', $fields) . '`';
                        $replace = implode(', ', array_fill(0, $total, '?'));
                        break;
                }
            }
            $placeholder['replaces'][$key] = $replace;
            $corrector = $corrector + $total - 1;

            foreach ($params as $i => $p) {
                array_splice($params[$i], $offset, 1, $params[$i][$offset]);
            }
        }

        if (!$is_multiple) {
            $params = $params[0];
        }
        if ($placeholder['replaces']) {
            $query = strtr($query, $placeholder['replaces']);
        }

        return $query;
    }

    /**
     * Escape a value that will be included in a query string
     *
     * @param int|string $value    The value to escape.
     *
     * @return string
     */
    public function quote($value): string
    {
        return $this->adapter->quote($value ?? '');
    }

    /**
     * Prepare an SQL statement for execution
     * 
     * @param string $query
     * 
     * @return StatementInterface
     */
    public function prepare(string $query): StatementInterface
    {
        $this->last_query = str_replace('#__', $this->prefix, $query);
        if ($this->profiler) {
            $this->queries[] = '[P] ' . $this->last_query;
        }

        return $this->adapter->prepare($this->last_query);
    }

    /**
     * Query
     * 
     * @param string $query
     */
    protected function query(string $query): ResultInterface
    {
        $this->last_query = str_replace('#__', $this->prefix, $query);
        if ($this->profiler) {
            $this->queries[] = $this->last_query;
        }
        return $this->adapter->query($this->last_query);
    }

    /**
     * Exec
     * 
     */
    protected function exec(string $query): int
    {
        $this->last_query = str_replace('#__', $this->prefix, $query);
        if ($this->profiler) {
            $this->queries[] = $this->last_query;
        }
        return $this->adapter->exec($this->last_query);
    }

    /**
     * Simple query
     *
     * @param string $query
     * @param array  $params
     * 
     * @return ResultInterface
     */
    protected function simpleQuery(string $query, array $params): ResultInterface
    {
        if (!$params) {
            return $this->query($query);
        }
        $stmt = $this->prepare($query);
        $stmt->execute($params);

        return $stmt->getResult();
    }

    /**
     * Count rows
     *
     * @param string $query
     * @param array  $params
     * 
     * @return int
     */
    protected function countRows(string $query, array $params): int
    {
        $wrapper = true;
        $query = preg_replace_callback('#\[([^\]]*)\](\*)?#m', function ($match) use (&$wrapper) {
            if (isset($match[2])) {
                $wrapper = false;
                return ' COUNT(*) ';
            }
            return '';
        }, $query);

        if ($wrapper) {
            $query = "SELECT COUNT(*) FROM ($query) AS t";
        }

        return $this->simpleQuery($query, $params)->fetchColumn() ?: 0;
    }

    /**
     * Get the last ID inserted
     * 
     * @return int|string
     */
    public function lastInsertId(): int|string
    {
        return $this->adapter->lastInsertId();
    }

    /**
     * Get last query
     * 
     * @return string
     */
    public function getLastQuery(): string
    {
        return $this->last_query;
    }

    /**
     * Get Queries
     * 
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * Get
     * 
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Get info
     * 
     * @return array
     */
    public function getInfo(): array
    {
        return $this->adapter->getInfo();
    }

    /**
     * Get
     * 
     * @return SchemaInterface
     */
    public function getSchema(): SchemaInterface
    {
        if ($this->schema === null) {
            $class = $this->adapter->resolveSchema();
            $this->schema = new $class($this);
        }

        return $this->schema;
    }

    /**
     * Get
     * 
     * @return \DatabasePrefixer
     */
    public function getPrefixer(): \DatabasePrefixer
    {
        if ($this->prefixer === null) {
            $this->prefixer = new \DatabasePrefixer($this->prefix);
        }

        return $this->prefixer;
    }
}
