<?php
/**
 * Created by PhpStorm.
 * User: Amrit
 * Date: 2017-10-05
 * Time: 2:42 PM
 */

namespace App\Controllers;

use App\Models\Company as CompanyModel;
use App\Models\CompanyAddress;

class Company extends BaseController
{
    private $requiredFields = [
        'name',
        'address',
        'city',
        'state',
        'zip'
    ];

    /**
     * Gets the requested resource
     *
     * @param $id
     */
    public function index($id)
    {
        $company = (new CompanyModel())->where('id', $id)->get()->toArray();
        $address = (new CompanyAddress())->where('company_id', $id)->get()->toArray();

        if (empty($company)) {
            errorCode(404, 'Not Found');
            return;
        }

        if (empty($address)) {
            $address = null;
        }
        $company['address'] = $address;

        $this->setOutput($company);
    }

    /**
     * Creates a new resource
     */
    public function create()
    {
        // sanity check the input
        $input = json_decode($this->getInput(), $array = true);
        if (false === $input || empty($input)) {
            errorCode(400, "Bad Request");
            return;
        }

        // ensure required fields are populated
        foreach ($this->requiredFields as $field) {
            if (empty($input[$field])) {
                errorCode(400, "Bad Request");
                return;
            }
        }

        // create a new company model object
        $company = new CompanyModel();
        $company->name = $input['name'];
        $company->description = $input['description'];
        $company->save();

        // and a new company address object
        $address = new CompanyAddress();
        $address->company_id = $company['id'];
        $address->address = $input['address'];
        $address->address_2 = $input['address2'];
        $address->city = $input['city'];
        $address->state = $input['state'];
        $address->zip = $input['zip'];
        $address->save();

        // combine the output
        $output = $company->toArray();
        $output['address'] = $address->toArray();
        $this->setOutput($output);
    }

    /**
     * Updates an existing resource by ID
     *
     * @param $id
     */
    public function update($id)
    {
        $company = (new CompanyModel())->where('id', $id)->get();
        $address = (new CompanyAddress())->where('id', $id)->get();

        if (empty($company->toArray())) {
            errorCode(404, 'Not Found');
        }
    }

    /**
     * Deletes a resource by ID
     *
     * @param $id
     */
    public function delete($id)
    {

    }

}