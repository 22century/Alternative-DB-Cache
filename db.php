<?php

trait AdcWpdbHooker {

    /**
     * @param string $query
     * @return bool|int
     */
    public function query($query) {
        $ret = null;
        // SELECT
        if (preg_match('/^SELECT.*/is', trim($query))) {

            $qhash = hash('crc32b', $query);
            if (apc_exists($qhash)) {
                $ret = $this->loadCache($qhash);
            } else {
                $ret = parent::query($query);
                $this->saveCache($qhash, $query);
            }

        } else {
            $ret = parent::query($query);
        }
        return $ret;
    }

    /**
     * @param string $key
     * @param string $query
     */
    protected function saveCache ($key, $query) {
        apc_add($key, [
            'last_result'   => $this->last_result,
            'result'        => $this->result,
            'col_info'      => null,
            'last_query'    => $query,
            'rows_affected' => $this->num_rows,
            'num_rows'      => $this->num_rows,
            'last_error'    => 0,
        ], $this->adc_ttl);
    }

    /**
     * @param string $key
     * @return int
     */
    protected function loadCache ($key) {
        $data = apc_fetch($key);

        $this->last_result   = $data['last_result'];
        $this->result        = $data['result'];
        $this->col_info      = $data['col_info'];
        $this->last_query    = $data['last_query'];
        $this->rows_affected = $data['rows_affected'];
        $this->num_rows      = $data['num_rows'];
        $this->last_error    = $data['last_error'];

        return $data['num_rows'];
    }
}

class AdcWpdb extends wpdb {

    use AdcWpdbHooker;

    protected $adc_ttl = 0;

    public function __construct () {

        $this->adc_ttl = defined('ADC_TTL') ? ADC_TTL : 60;

        parent::__construct(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
    }

}

$GLOBALS['wpdb'] = new AdcWpdb();
