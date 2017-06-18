<?php

namespace Gist\LocationBundle\Controller;

use Gist\TemplateBundle\Model\CrudController;
use Gist\LocationBundle\Entity\POSLocations;
use Gist\InventoryBundle\Model\Gallery;
use Gist\ValidationException;

use DateTime;

class POSLocationsController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'gist_loc_pos_locations';
        $this->title = 'POS Location';

        $this->list_title = 'POS Locations';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        return new POSLocations();
    }

    protected function getObjectLabel($obj)
    {
        return $obj->getName();
    }

    protected function getGridJoins()
    {
        $grid = $this->get('gist_grid');
        return array(
            $grid->newJoin('a', 'area', 'getArea'),
        );
    }

    protected function getGridColumns()
    {
        $grid = $this->get('gist_grid');

        return array(
            $grid->newColumn('Location', 'getName', 'name'),
            $grid->newColumn('Area', 'getName', 'name','a'),
            $grid->newColumn('Contact No.', 'getContactNumber', 'contact_number'),
            $grid->newColumn('Location', 'getLocatorDesc', 'locator_desc'),
        );
    }

    protected function padFormParams(&$params, $o = null)
    {
        $em = $this->getDoctrine()->getManager();
        $am = $this->get('gist_accounting');
        $params['bank_opts'] = $am->getBankOptions();

        // enabled options
        $params['type_opts'] = array(
            'Kiosk' => 'Kiosk',
            'Shop' => 'Shop',
            'Inline' => 'Inline',
            'Shop in shop' => 'Shop in shop',
            'Hybrid' => 'Hybrid'
        );

        $params['brand_opts'] = array(
            'Aqua Mineral' => 'Aqua Mineral',
            'Botanifique' => 'Botanifique',
            'ELEVATIONE' => 'ELEVATIONE'
        );

        $params['status_opts'] = array(
            'Active' => 'Active',
            'Inactive' => 'Inactive',
            'Deleted' => 'Deleted'
        );

        $params['terminals'] = $em->getRepository('GistAccountingBundle:Terminal')->findBy(array('actual_location'=>$o->getID()));

        $params['area_opts'] = $this->getAreaOptions();

        return $params;
    }

    protected function update($o, $data, $is_new = false)
    {
        $media = $this->get('gist_media');

        $o->setName($data['name']);
        $o->setLeasor($data['leasor']);
        $o->setContactNumber($data['contact_number']);
        $o->setCoordinates($data['coordinates']);
        $o->setLocatorDesc($data['locator_desc']);
        $o->setType($data['type']);
        $o->setBrand($data['brand']);
        $o->setCity($data['city']);
        $o->setPostal($data['postal']);
        $o->setRegion($data['region']);
        $o->setCountry($data['country']);
        $o->setStatus($data['status']);

        $em = $this->getDoctrine()->getManager();
        if (isset($data['area'])) {
            $area = $em->getRepository('GistLocationBundle:Areas')->find($data['area']);
            $o->setArea($area);
        }



        // PERMITS
        if($data['upl_barangay_clearance']!=0 && $data['upl_barangay_clearance'] != ""){
            $o->setBarangayClearance($media->getUpload($data['upl_barangay_clearance']));
        }

        if (isset($data['exp_barangay_clearance'])) {
            $o->setBarangayClearanceExpiration(new DateTime($data['exp_barangay_clearance']));
        }

        if($data['upl_bir0605']!=0 && $data['upl_bir0605'] != ""){
            $o->setBir0605($media->getUpload($data['upl_bir0605']));
        }

        if (isset($data['exp_bir0605'])) {
            $o->setBir0605Expiration(new DateTime($data['exp_bir0605']));
        }

        if($data['upl_bir0605']!=0 && $data['upl_bir0605'] != ""){
            $o->setBir0605($media->getUpload($data['upl_bir0605']));
        }

        if (isset($data['exp_bir0605'])) {
            $o->setBir0605Expiration(new DateTime($data['exp_bir0605']));
        }
        ///////

        if($data['upl_mayors_permit']!=0 && $data['upl_mayors_permit'] != ""){
            $o->setMayorsPermit($media->getUpload($data['upl_mayors_permit']));
        }

        if (isset($data['exp_mayors_permit'])) {
            $o->setMayorsPermitExpiration(new DateTime($data['exp_mayors_permit']));
        }
        ///////

        if($data['upl_bir2303']!=0 && $data['upl_bir2303'] != ""){
            $o->setBir2303($media->getUpload($data['upl_bir2303']));
        }

        if (isset($data['exp_bir2303'])) {
            $o->setBir2303Expiration(new DateTime($data['exp_bir2303']));
        }
        ///////

        if($data['upl_fire_permit']!=0 && $data['upl_fire_permit'] != ""){
            $o->setFirePermit($media->getUpload($data['upl_fire_permit']));
        }

        if (isset($data['exp_fire_permit'])) {
            $o->setFirePermitExpiration(new DateTime($data['exp_fire_permit']));
        }
        ///////

        if($data['upl_sanitary_permit']!=0 && $data['upl_sanitary_permit'] != ""){
            $o->setSanitaryPermit($media->getUpload($data['upl_sanitary_permit']));
        }

        if (isset($data['exp_sanitary_permit'])) {
            $o->setSanitaryPermitExpiration(new DateTime($data['exp_sanitary_permit']));
        }
        ///////


        // RENTAL
        $o->setRentPaymentAmount($data['rental_payment_amount']);
        $o->setRentPaymentDue($data['rental_due_date']);
        $o->setRentSecurityDepositAmount($data['security_deposit_amount']);
        $o->setRentSecurityDepositReturned($data['security_deposit_returned']);
        $o->setRentSecurityDepositReturnedAmount($data['security_deposit_amount_returned']);
        $o->setRentSecurityDepositRemarks($data['security_deposit_remarks']);

        if($data['design_criteria']!=0 && $data['design_criteria'] != ""){
            $o->setRentDesignCriteria($media->getUpload($data['design_criteria']));
        }

        $o->setRentUnitNumber($data['unit_no']);
        $o->setRentDimension($data['dimension_meter']);
        $o->setRentPricePerSqMeter($data['price_sq_meter']);
        $o->setRentContactPerson($data['contact_person']);
        $o->setRentCpPosition($data['contact_position']);
        $o->setRentCpContactNumber($data['contact_number']);
        $o->setRentCpEmail($data['contact_email']);


        //INSURANCE

        $o->setInsuranceCompany($data['insurance_company']);
        $o->setInsuranceExpiration(new DateTime($data['insurance_expiration']));
        $o->setInsurancePolicy($data['insurance_policy']);

        if($data['insurance_policy_document']!=0 && $data['insurance_policy_document'] != ""){
            $o->setInsurancePolicyDocument($media->getUpload($data['insurance_policy_document']));
        }

        $o->setInsuranceContactPerson1($data['contact_person']);
        $o->setInsuranceContactNumber1($data['contact_position']);
        $o->setInsuranceContactPerson2($data['contact_number']);
        $o->setInsuranceContactNumber2($data['contact_email']);

    }

    protected function getOptionsArray($repo, $filter, $order, $id_method, $value_method)
    {
        $em = $this->getDoctrine()->getManager();
        $objects = $em->getRepository($repo)
            ->findBy(
                $filter,
                $order
            );

        $opts = array();
        foreach ($objects as $o)
            $opts[$o->$id_method()] = $o->$value_method();

        return $opts;
    }

    public function getAreaOptions($filter = array())
    {
        return $this->getOptionsArray(
            'GistLocationBundle:Areas',
            $filter, 
            array('name' => 'ASC'),
            'getID',
            'getName'
        );
    }


}
