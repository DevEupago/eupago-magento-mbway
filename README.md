euPago MbWay payment gateway extension for Magento 2


Install using File Upload:


1) At Magento 2 root folder go to the **app/code/** folder and create the structure **Eupago/MbWay**

2) Put the content of the ZIP file inside the folder **MbWay** created at step 2.

3) Execute comands above:

		3.1) bin/magento module:enable --clear-static-content Eupago_MbWay

		3.2) bin/magento setup:upgrade

		3.3) bin/magento setup:di:compile

		3.4) bin/magento setup:static-content:deploy -f
  

4) Enable and configure Eupago Multibanco in your Backoffice Magento 2 under "Stores/Configuration/Payment Methods/Eupago MbWay"

