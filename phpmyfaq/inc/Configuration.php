<?php
/**
* $Id: Configuration.php,v 1.8 2006-08-25 17:15:58 matteo Exp $
*
* The main class for fetching the configuration, update and delete items.
*
* @author       Thorsten Rinne <thorsten@phpmyfaq.de>
* @package      phpMyFAQ
* @since        2006-01-04
* @copyright    (c) 2006 phpMyFAQ Team
*
* The contents of this file are subject to the Mozilla Public License
* Version 1.1 (the "License"); you may not use this file except in
* compliance with the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
*
* Software distributed under the License is distributed on an "AS IS"
* basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
* License for the specific language governing rights and limitations
* under the License.
*/
class PMF_Configuration
{
    /**
    * DB handle
    *
    * @var  object
    */
    var $db;

    /**
    * Configuration array
    *
    * @var  array
    */
    var $config = array();

    /**
    * Constructor
    *
    * @param    object
    * @return   void
    * @author   Thorsten Rinne <thorsten@phpmyfaq.de>
    */
    function PMF_Configuration(&$db)
    {
        $this->db = &$db;
    }

    /**
    * Fetches all configuration items into an array
    *
    * @return   void
    * @access   public
    * @author   Thorsten Rinne <thorsten@phpmyfaq.de>
    */
    function getAll()
    {
        $query = sprintf("
            SELECT
                config_name, config_value
            FROM
                %sfaqconfig",
            SQLPREFIX);
        $result = $this->db->query($query);
        while ($row = $this->db->fetch_object($result)) {
            // Fix the boolean values
            $isTrueValue  = ('1' == ($row->config_value)) || ('true'  == ($row->config_value));
            $isFalseValue = (''  == ($row->config_value)) || ('false' == ($row->config_value));
            if ($isTrueValue || $isFalseValue) {
                $this->config[$row->config_name] = $isTrueValue;
            } else {
                $this->config[$row->config_name] = $row->config_value;
            }
        }
    } // end func fetchAll()

    /**
    * Returns a configuration item
    *
    * @param    string
    * @return   mixed
    * @access   public
    * @author   Thorsten Rinne <thorsten@phpmyfaq.de>
    */
    function get($item)
    {
        if (!isset($this->config[$item])) {
            $this->getAll();
        }
        return $this->config[$item];
    } // end func get()

    /**
    * Updates all configuration items
    *
    * @param    mixed
    * @return   bool
    * @access   public
    * @author   Thorsten Rinne <thorsten@phpmyfaq.de>
    */
    function update($newconfig)
    {
        if (is_array($newconfig)) {
            foreach ($newconfig as $name => $value) {
                $query = sprintf("
                    UPDATE
                        %sfaqconfig
                    SET
                        config_value = '%s'
                    WHERE
                        config_name = '%s'",
                    SQLPREFIX,
                    $this->db->escape_string($value),
                    $name
                    );
                $this->db->query($query);
            }
            return true;
        }
        return false;
    } // end func update()
}
