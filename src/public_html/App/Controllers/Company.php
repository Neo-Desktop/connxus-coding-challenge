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
    /**
     * Required fields for creation of a company
     *
     * @var array
     */
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
        $company = (new CompanyModel())->where('id', $id)->find()->toArray();
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

        // create new company and address models
        $company = new CompanyModel();
        $address = new CompanyAddress();

        // pass off to the update routine
        $this->update(null, $company, $address);
    }

    /**
     * Updates an existing resource by ID
     *
     * @param      $id
     * @param null $company
     * @param null $address
     */
    public function update($id, $company = null, $address = null)
    {
        // check if we weren't passed in from create
        if ($id !== null) {
            $company = (new CompanyModel())->where('id', $id)->find();
            $address = (new CompanyAddress())->where('company_id', $id)->get();

            if (empty($company->toArray())) {
                errorCode(404, 'Not Found');
                return;
            }
        }

        // sanity check the input
        $input = json_decode($this->getInput(), $array = true);
        if (false === $input || empty($input)) {
            errorCode(400, "Bad Request");
            return;
        }

        // update the company values
        $company->name = $input['name'];
        $company->description = $input['description'];
        $company->save();

        $output = $company->toArray();
        $output['address'] = [];

        // handle multiple address entries.
        // must specify id in payload
        if (is_array($input['address'])) {
            foreach ($input['address'] as $addressElement) {
                $addressObject = (new CompanyAddress())
                    ->where('id', $addressElement['id'])
                    ->where('company_id', $company['id'])
                    ->find();

                if (empty($addressObject->toArray())) {
                    errorCode(404, 'Not Found');
                    return;
                }

                // update the address values
                $addressObject->company_id = $company['id'];
                $addressObject->address = $addressElement['address'];
                $addressObject->address_2 = $addressElement['address2'];
                $addressObject->city = $addressElement['city'];
                $addressObject->state = $addressElement['state'];
                $addressObject->zip = $addressElement['zip'];
                $addressObject->save();

                $output['address'][] = $addressObject->toArray();
            }
        }
        else {
            // update the address values
            $address->company_id = $company['id'];
            $address->address = $input['address'];
            $address->address_2 = $input['address2'];
            $address->city = $input['city'];
            $address->state = $input['state'];
            $address->zip = $input['zip'];
            $address->save();

            $output['address'][] = $address->toArray();
        }

        // generate the output
        $this->setOutput($output);
    }

    /**
     * Deletes a resource by ID
     *
     * @param $id
     */
    public function delete($id)
    {
        // gather all company and address entries for the ID
        $company = (new CompanyModel())->where('id', $id)->find();
        $addresses = (new CompanyAddress())->where('id', $id)->get();

        // return an error if not found
        if (empty($company->toArray())) {
            errorCode(404, 'Not Found');
            return;
        }

        // delete the entries
        $company->delete();
        foreach ($addresses as $address) {
            $address->delete();
        }

        // display a success message
        $out = [
            'success' => true,
            'id' => $id,
        ];

        // generate the output
        $this->setOutput($out);
    }

}