# The "Allocadence SKU Re-Map" prototype

This project scans the orders.csv downloaded from Allocadence and the accounting.csv downloaded from the Job Board, it 

* aggregates needed fields from orders, 
* joins the SKU data to the CSV for Accounting data,
 * and attempts to fill empty fields (very duck taped at the moment)

**PLEASE NOTE:** The code is very "experimental", it's a decent prototype, but there is still so much improvement 
that can be done and it may have errors that haven't been discovered yet due to lack of thorough testing. 

To run use a command line and relative to the `alloc-sku-remap` folder type:

    php index.php 



## No composer libs or pecl extensions are needed

The CLI app just uses Native PHP 7 