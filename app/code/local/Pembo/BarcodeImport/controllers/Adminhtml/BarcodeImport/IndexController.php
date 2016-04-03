<?php

class Pembo_BarcodeImport_Adminhtml_BarcodeImport_IndexController extends Mage_Adminhtml_Controller_Action
{

    const UTF8_BOM = "\xEF\xBB\xBF";

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/barcode_import')
            ->_addBreadcrumb(Mage::helper('barcode_import')->__('Catalog'), Mage::helper('barcode_import')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('barcode_import')->__('Upload New File'), Mage::helper('barcode_import')->__('Upload New File'));
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }



    public function saveAction() {
        if ($this->getRequest()->getPost()) {
            $params = $this->getRequest()->getPost();


            $path = Mage::getBaseDir('media') . DS . 'barcodes' . DS;
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }


            $newFilename = $this->uploadCsv('csvfile', $path);

            if (isset($newFilename)) {

                $ret = $this->insertRecords($path . $newFilename);

                if ($ret !== true) {
                    // It wasn't a success... we have errors..
                    foreach ($ret as $err) {
                        Mage::getSingleton('adminhtml/session')->addError($err);
                    }
                }
            }
        }
        $this->_redirect('*/*/');
    }


    public function uploadCsv($fname, $path) {

        if ($_FILES[$fname]['error']) {

            $text = $this->_codeToMessage($_FILES[$fname]['error']);
            $this->_getSession()->addError($text);
            return false;
        }

        if (isset($_FILES[$fname]['name']) and ( file_exists($_FILES[$fname]['tmp_name']))) {

            try {
                $uploader = new Varien_File_Uploader($fname);
                $uploader->setAllowedExtensions(array('csv')); // or pdf or anything
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);


                $info = pathinfo($_FILES[$fname]['name']);

                if (strtolower($info['extension']) != 'csv') {
                    throw new Exception('Files must be uploaded in CSV Format only');
                }

                $newFilename = date('y_m_d_h_i_s') . '.' . $info['extension'];

                $uploader->save($path, $newFilename);

                $this->_getSession()->addSuccess('Your file was uploaded');

                Mage::app()->getCacheInstance()->cleanType('block_html');

            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
            }

            return $newFilename;
        }
    }

    public function insertRecords($csvFile) {
        ini_set("auto_detect_line_endings", true);

        $errCollection	= array();

        $row = 1;

        if (($handle = fopen($csvFile, "r")) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $row++; // Counter used to track errors.

                if (!isset($headers)) {
                    $headers = $this->_getCsvHeaders($data);

                    $result = $this->validateHeaders($headers);

                    if($result !== true) {
                        $errCollection = $result;
                        break;
                    }

                    continue;
                }

                // Combine the headers and the data into one array.. thanks Smarty for the function recco.
                $data = array_combine($headers, $data);

                if(empty($data['sku'])) {
                    continue;
                }

                if(empty($data['barcode'])) {
                    $data['barcode'] = 'Not Applicable';
                }

                /** @var Pembo_BarcodeImport_Model_Pending $pending */
                $pending = Mage::getModel('barcode_import/pending');
                $pending->setData(array(
                    'sku' => $data['sku'],
                    'barcode' => $data['barcode'],
                    'created_at' => time(),
                    'updated_at' => time()
                ));

                $pending->save();
                $pending->clearInstance();
            }

            $this->_scheduleJob();

            fclose($handle);
        } else {
            $errCollection['read_error'] = 'Failed to open file for reading';
        }

        if (empty($errCollection)) {
            return true;
        }

        return $errCollection;
    }

    protected function _scheduleJob()
    {


        $cron = Mage::getModel('cron/schedule');
        $cron->setData(array(
            'job_code'		=> 'barcode_import_run',
            'status'		=> 'pending',
            'created_at'	=> gmdate('Y-m-d H:i:s'),
            'scheduled_at'	=> gmdate('Y-m-d H:i:\0\0', strtotime('+5 minutes')),
        ));

        $cron->save();
    }

    protected function _getCsvHeaders($data)
    {
        // get rid of UTF8 BOM if present
        if (strpos($data[0], self::UTF8_BOM) !== false) {
            $isUtf8 = true;
            $data[0] = str_replace(self::UTF8_BOM, '', $data[0]);
        }

        return $data;
    }


    public function validateHeaders($headers)
    {
        $errCollection = array();

        if($headers[0] == 'sku' && $headers[1] == 'barcode') {
            return true;
        } else {
            $errCollection['header_error'] = 'Invalid CSV Headers';
        }


        return $errCollection;
    }



    protected function _isAllowed() {
        return true; // TODO FIX
        //return Mage::getSingleton('admin/session')->isAllowed('system/index');
    }

    private function _codeToMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }

        return $message;
    }


}
