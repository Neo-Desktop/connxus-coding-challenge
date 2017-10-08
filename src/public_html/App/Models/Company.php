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
 * Class Company
 *
 * @package App\Models
 *
 * @property int      id
 * @property string   name
 * @property DateTime created_time
 * @property DateTime updated_time
 * @property string   description
 */
class Company extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Gets the address object we want
     */
    public function address()
    {
        $this->addWith('address', CompanyAddress::class, 'id', 'company_id');
    }
}