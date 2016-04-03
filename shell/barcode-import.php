<?php
require_once 'abstract.php';

class Mage_Shell_Pembo_BarcodeImport extends Mage_Shell_Abstract {

    const CLASS_SHORT = 'barcode_import/console';

    public function run()
    {
        try {

            $action = $this->getArg('action');
            if (empty($action)) {
                echo $this->usageHelp();
            } else {
                $actionMethodName = $action . 'Action';
                $cron = Mage::getModel(self::CLASS_SHORT);


                if (method_exists($cron, $actionMethodName)) {
                    // emulate index.php entry point for correct URLs generation in scheduled cronjobs
                    Mage::register('custom_entry_point', true);
                    // Disable use of SID in generated URLs - This is standard for cron job bootstrapping
                    Mage::app()->setUseSessionInUrl(false);
                    // Disable permissions masking by default - This is Magento standard, but not recommended for security reasons
                    umask(0);
                    // Load the global event area - This is non-standard be should be standard
                    Mage::app()->addEventArea(Mage_Core_Model_App_Area::AREA_GLOBAL);
                    // Load the crontab event area - This is standard for cron job bootstrapping
                    Mage::app()->addEventArea('crontab');


                    // Run the command
                    $cron->$actionMethodName();
                } else {
                    echo "Action $action not found!\n";
                    echo $this->usageHelp();
                    exit(1);
                }
            }
        } catch (Exception $e) {
            $fh = fopen('php://stderr', 'w');
            fputs($fh, $e->__toString());
            fclose($fh);
            exit(255);
        }
    }


    public function usageHelp() {
        $class = Mage::getModel(self::CLASS_SHORT);

        $help = "\n" . 'You may use the following actions below in the format ' . "\n\n";
        $help .= '     php ' . basename( __FILE__) . ' --action {action}' . "\n\n\n";
        $help .= 'Available actions: ' . "\n";
        $methods = get_class_methods($class);

        foreach ($methods as $method) {
            if (substr($method, -6) == 'Action') {
                $help .= '    ' . substr($method, 0, -6);
                $helpMethod = $method . 'Help';


                if (method_exists($class, $helpMethod)) {
                    $help .= ' - ' . $class->$helpMethod();
                }
                $help .= "\n";
            }
        }
        return $help;
    }
}

require_once '../app/Mage.php';

$shell = new Mage_Shell_Pembo_BarcodeImport();
$shell->run();
