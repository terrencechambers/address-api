<?php

namespace Code4KC\Address;

use \PDO as PDO;

/**
 * Class Address
 */
class Address extends BaseTable
{

    var $table_name = 'address';
    var $primary_key_sequence = 'address_id_seq_02';
    var $single_line_address_query = '';
    var $neighbornood_query = '';
    var $typeahead_query = '';
    var $get_attributes_query = '';
    var $fields = array(
        'single_line_address' => '',
        'street_number' => '',
        'pre_direction' => '',
        'street_name' => '',
        'street_type' => '',
        'post_direction' => '',
        'internal' => '',
        'city' => '',
        'state' => '',
        'zip' => '',
        'zip4' => '',
        'longitude' => '0.0',
        'latitude' => '0.0'
    );

    function get_neighborhood($nighborhood)
    {

        if (!$this->neighbornood_query) {
            $sql = 'SELECT 
                a.id AS address_id,
                a.single_line_address,
                a.city,
                a.state,
                a.zip,
                a.longitude,
                a.latitude,
                 
                k.city_address_id AS city_id,

                c.land_use_code AS city_land_use_code,
                c.land_use AS city_land_use,
                c.classification AS city_classification,
                c.sub_class AS city_sub_class,
                c.neighborhood AS city_nighborhood,
                c.council_district AS city_council_district,

                k.county_address_id AS county_id,
                b.block_2010_name AS census_block_2010_name,
                b.block_2010_id AS census_block_2010_id,
                b.tract_name AS census_track_name,
                b.tract_id AS census_track_id,
                b.zip AS census_zip,
                b.county_id AS census_county_id,
                b.state_id AS census_county_state_id,
                b.longitude AS census_longitude,
                b.latitude AS census_latitude,
                b.tiger_line_id AS census_tiger_line_id


                FROM address_keys k
                LEFT JOIN address a on a.id = k.address_id
                LEFT JOIN census_attributes b ON b.city_address_id = k.city_address_id
                LEFT JOIN city_address_attributes c ON c.id = k.city_address_id
                LEFT JOIN county_address_attributes j ON j.id = k.county_address_id
                WHERE UPPER(c.neighborhood) = :nighborhood';
            $this->neighbornood_query = $this->dbh->prepare("$sql  -- " . __FILE__ . ' ' . __LINE__);
        }

        try {
            $this->neighbornood_query->execute(array(':nighborhood' => strtoupper($nighborhood)));
        } catch (PDOException  $e) {
            error_log($e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__);
            //throw new Exception('Unable to query database');
            return false;
        }

        return $this->neighbornood_query->fetchAll(PDO::FETCH_ASSOC);

    }

    /**
     * @param $id
     * @return false or found record
     */
    function typeahead($single_line_address)
    {

        $single_line_address = strtoupper( $single_line_address );
        $single_line_address .= '%';

        if (!$this->typeahead_query) {
            $sql = 'SELECT id, single_line_address  FROM ' . $this->table_name . ' WHERE single_line_address LIKE :single_line_address LIMIT 50';
            $this->typeahead_query = $this->dbh->prepare("$sql  -- " . __FILE__ . ' ' . __LINE__);
        }

        try {
            $this->typeahead_query->execute(array(':single_line_address' => $single_line_address ));
        } catch (PDOException  $e) {
            error_log($e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__);
            //throw new Exception('Unable to query database');
            return false;
        }
        return $this->typeahead_query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param $id
     * @return false or found record
     */
    function find_by_single_line_address($single_line_address)
    {
        if (!$this->single_line_address_query) {
            $sql = 'SELECT *  FROM ' . $this->table_name . ' WHERE single_line_address = :single_line_address';
            $this->single_line_address_query = $this->dbh->prepare("$sql  -- " . __FILE__ . ' ' . __LINE__);
        }

        try {
            $this->single_line_address_query->execute(array(':single_line_address' => $single_line_address));
        } catch (PDOException  $e) {
            error_log($e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__);
            //throw new Exception('Unable to query database');
            return false;
        }

        return $this->single_line_address_query->fetch(PDO::FETCH_ASSOC);
    }

    function get_attributes($address_id)
    {

        if (!$this->get_attributes_query) {
            $sql = 'SELECT 
                k.city_address_id AS city_id,
                c.land_use_code AS city_land_use_code,
                c.land_use AS city_land_use,
                c.classification AS city_classification,
                c.sub_class AS city_sub_class,
                c.neighborhood AS city_nighborhood,
                c.council_district AS city_council_district,

                k.county_address_id AS county_id,
                b.block_2010_name AS census_block_2010_name,
                b.block_2010_id AS census_block_2010_id,
                b.tract_name AS census_track_name,
                b.tract_id AS census_track_id,
                b.zip AS census_zip,
                b.county_id AS census_county_id,
                b.state_id AS census_county_state_id,
                b.longitude AS census_longitude,
                b.latitude AS census_latitude,
                b.tiger_line_id AS census_tiger_line_id


                FROM address_keys k
                LEFT JOIN census_attributes b ON b.city_address_id = k.city_address_id
                LEFT JOIN city_address_attributes c ON c.id = k.city_address_id
                LEFT JOIN county_address_attributes j ON j.id = k.county_address_id
                WHERE k.address_id = :address_id';
            $this->get_attributes_query = $this->dbh->prepare("$sql  -- " . __FILE__ . ' ' . __LINE__);
        }

        try {
            $this->get_attributes_query->execute(array(':address_id' => $address_id));
        } catch (PDOException  $e) {
            error_log($e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__);
            //throw new Exception('Unable to query database');
            return false;
        }

        return $this->get_attributes_query->fetch(PDO::FETCH_ASSOC);

    }

}
