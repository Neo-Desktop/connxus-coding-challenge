<?php
/**
 * Created by PhpStorm.
 * User: Amrit
 * Date: 2017-10-06
 * Time: 9:14 PM
 */

namespace App\Models;


use DateTime;

/**
 * Class CompanyAddress
 *
 * @package App\Models
 *
 * @property int      id
 * @property int      company_id
 * @property string   address
 * @property string   address_2
 * @property string   city
 * @property string   state
 * @property string   zip
 * @property float    latitude
 * @property float    longitude
 * @property DateTime created_time
 */
class CompanyAddress extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_address';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Maps column [key] to output [value] in toArray();
     *
     * @var array
     */
    protected $columnMap = [
        'address_2' => 'address2'
    ];
}