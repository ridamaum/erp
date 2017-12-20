<?php

namespace Gist\InventoryBundle\Controller;

use Gist\TemplateBundle\Model\CrudController;
use Gist\InventoryBundle\Entity\StockTransfer;
use Gist\InventoryBundle\Entity\StockTransferEntry;
use Gist\CoreBundle\Template\Controller\TrackCreate;
use DateTime;

class StockTransferController extends CrudController
{
    use TrackCreate;
    public function __construct()
    {
        $this->route_prefix = 'gist_inv_stock_transfer';
        $this->title = 'Stock Transfer';

        $this->list_title = 'Stock Transfer';
        $this->list_type = 'dynamic';
        $this->repo = "GistInventoryBundle:StockTransfer";
    }

    public function indexAction()
    {
        $this->checkAccess($this->route_prefix . '.view');

        $this->hookPreAction();
        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List', 'gist_inv_stock_transfer_index');

        $date_from = new DateTime();
        $date_to = new DateTime();
        $date_from->format("Y-m-d");
        $date_to->format("Y-m-d");

        $this->padFormParams($params, $date_from, $date_to);
        $twig_file = 'GistInventoryBundle:StockTransfer:index.html.twig';


        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();
        return $this->render($twig_file, $params);
    }

    public function callbackGrid($id)
    {
        $params = array(
            'id' => $id,
            'route_edit' => $this->getRouteGen()->getEdit(),
            'route_delete' => $this->getRouteGen()->getDelete(),
            'prefix' => $this->route_prefix,
        );

        $this->padGridParams($params, $id);

        $engine = $this->get('templating');
        return $engine->render(
            'GistInventoryBundle:StockTransfer:action.html.twig',
            $params
        );
    }

//    public function viewAction()
//    {
//        $this->hookPreAction();
//        $gl = $this->setupGridLoader();
//        $params = $this->getViewParams('List', 'hris_report_attendance_view');
//        $twig_file = 'HrisReportBundle:Attendance:view.html.twig';
//        $date_from = new DateTime();
//        $date_to = new DateTime();
//        $date_from->format("Y-m-d");
//        $date_to->format("Y-m-d");
//        $this->padFormParams($params, $date_from, $date_to);
//        $params['date_from'] = $date_from;
//        $params['date_to'] = $date_to;
//        $params['list_title'] = $this->list_title;
//        return $this->render($twig_file, $params);
//    }

    protected function getObjectLabel($obj)
    {
        if ($obj == null)
        {
            return '';
        }
        return $obj->getID();
    }

    protected function newBaseClass()
    {
        return new StockTransfer();
    }

    protected function getGridJoins()
    {
        $grid = $this->get('gist_grid');
        return array(
            $grid->newJoin('d_inv','destination_inv_account','getDestination'),
            $grid->newJoin('s_inv','source_inv_account','getSource'),
        );
    }


    protected function getGridColumns()
    {
        $grid = $this->get('gist_grid');
        return array(
            $grid->newColumn('ID','getID','id'),
            $grid->newColumn('Status','getStatus','status'),
            $grid->newColumn('Source','getName','name','s_inv'),
            $grid->newColumn('Destination','getName','name','d_inv'),
        );
    }

    protected function padFormParams(&$params, $object = NULL){
        $em = $this->getDoctrine()->getManager();

        $inv = $this->get('gist_inventory');
        $params['wh_opts'] = array('-1'=>'-- Select Location --') + array('0'=>'Main Warehouse') + $inv->getPOSLocationOptions();
        $params['item_opts'] = array('000'=>'-- Select Product --') + $inv->getProductOptionsTransfer();
        return $params;
    }

    protected function add($obj)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $this->getRequest()->request->all();

        // validate
        $this->validate($data, 'add');

        // update db
        $this->update($obj, $data, true);

        $em->persist($obj);
        $em->flush();
        $this->hookPostSave($obj,true);

        // log
        $odata = $obj->toData();
        $this->logAdd($odata);
    }

    protected function update($o, $data, $is_new = false)
    {
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('gist_inventory');
        $config = $this->get('gist_configuration');



        if ($is_new) {
            $o->setStatus('requested');
        }

        // initialize entries
        $entries = array();

        // warehouse
        if ($data['source'] == 0) {
            $wh_src = $inv->findWarehouse($config->get('gist_main_warehouse'));
        } else {
            $wh_src = $em->getRepository('GistLocationBundle:POSLocations')->find($data['source']);
        }

        if ($data['destination'] == 0) {
            $wh_destination = $inv->findWarehouse($config->get('gist_main_warehouse'));
        } else {
            $wh_destination = $em->getRepository('GistLocationBundle:POSLocations')->find($data['destination']);
        }

        $o->setDescription($data['description']);
        $o->setSource($wh_src->getInventoryAccount());
        $o->setDestination($wh_destination->getInventoryAccount());

        $em->persist($o);
        $em->flush();


        foreach ($data['product_item_code'] as $index => $value)
        {
            $prod_item_code = $value;
            $qty = $data['quantity'][$index];



            // product
            $prod = $em->getRepository('GistInventoryBundle:Product')->findOneBy(array('item_code'=>$prod_item_code));
            if ($prod == null)
                throw new ValidationException('Could not find product.');

            //from src
            $entry = new StockTransferEntry();
            $entry->setStockTransfer($o)
                ->setProduct($prod)
                ->setQuantity($qty);

            $em->persist($entry);
            $em->flush();


            $em->persist($entry);
            $em->flush();

            $entries[] = $entry;
        }


        return $entries;
    }


    protected function hookPostSave($obj, $is_new = false)
    {

    }

}