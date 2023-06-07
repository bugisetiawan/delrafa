<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'Dashboard';
$route['404_override'] = 'Auth/show_404';
$route['translate_uri_dashes'] = FALSE;

// Dashboard
$route['dashboard'] = 'Dashboard/dashboard';

// General
$route['general/out_stock_product'] = 'General/out_stock_product';
$route['general/more_stock_product'] = 'General/more_stock_product';
$route['general/due_purchase_invoice'] = 'General/due_purchase_invoice';
$route['general/due_sales_invoice'] = 'General/due_sales_invoice';
$route['general/low_selling_price'] = 'General/low_selling_price';
$route['general/product_list'] = 'General/product_list';
$route['general/product_sales_history'] = 'General/product_sales_history';
$route['general/cancel_do'] = 'General/cancel_do';

// Auth
$route['login'] = 'Auth';
$route['logout'] = 'Auth/logout';
$route['denied'] = 'Auth/show_access_denied';
$route['maintenance'] = 'Auth/maintenance';

// Master -> Product
$route['product'] = 'master/Product';
$route['product/create'] = 'master/Product/create';
$route['product/detail/(:any)'] = 'master/Product/detail/$1';
$route['product/barcode/(:any)'] = 'master/Product/form_barcode/$1';
$route['product/update/(:any)'] = 'master/Product/update/$1';
$route['product/delete'] = 'master/Product/delete';
$route['product/price_list'] = 'master/Product/price_list';
$route['product/print/barcode'] = 'master/Product/print_barcode';
$route['product/print/stock_card/(:any)'] = 'master/Product/print_stock_card/$1';
$route['product/print/stock_product'] = 'master/Product/print_stock_product';
$route['product/print/price_list'] = 'master/Product/print_price_list';
$route['product/import/master'] = 'master/Product/import_master_product';
$route['product/import/hpp_buyprice'] = 'master/Product/import_product_hpp_buyprice';
$route['product/export/sellprice'] = 'master/Product/export_product_sellprice';
$route['product/import/sellprice'] = 'master/Product/import_product_sellprice';
$route['product/export/min_max_stock'] = 'master/Product/export_min_max_stock';
$route['product/import/min_max_stock'] = 'master/Product/import_min_max_stock';
// Master -> Customer
$route['customer'] = 'master/Customer';
$route['customer/add'] = 'master/Customer/add';
$route['customer/detail/(:any)'] = 'master/Customer/detail/$1';
$route['customer/update/(:any)'] = 'master/Customer/update/$1';
$route['customer/delete'] = 'master/Customer/delete';
// Master -> Supplier
$route['supplier'] = 'master/Supplier';
$route['supplier/add'] = 'master/Supplier/add';
$route['supplier/detail/(:any)'] = 'master/Supplier/detail/$1';
$route['supplier/update/(:any)'] = 'master/Supplier/update/$1';
$route['supplier/delete'] = 'master/Supplier/delete';
// Master -> Employee
$route['employee'] = 'master/Employee';
$route['employee/add'] = 'master/Employee/add';
$route['employee/detail/(:any)'] = 'master/Employee/detail/$1';
$route['employee/update/(:any)'] = 'master/Employee/update/$1';
$route['employee/delete'] = 'master/Employee/delete';
// Master -> Department
$route['department'] = 'master/Department';
$route['department/data_department'] = 'master/Department/data_department';
// Master -> Warehouse
$route['warehouse'] = 'master/Warehouse';
// Master -> Unit
$route['unit'] = 'master/Unit';
// Master -> Depreciation
$route['depreciation'] = 'master/Depreciation';
// Master -> Cost
$route['cost'] = 'master/Cost';
// Master -> Position
$route['position'] = 'master/Position';
// Master -> Bank
$route['bank'] = 'master/Bank';
$route['bank/account'] = 'master/Bank/bank_account';
// Master -> Zone
$route['zone'] = 'master/Zone';
// Master -> Education
$route['education'] = 'master/Education';
// Master -> Religion
$route['religion'] = 'master/Religion';

// Transaction -> Purchase Order
$route['purchase/order'] = 'transaction/Purchase/purchase_order';
$route['purchase/order/add'] = 'transaction/Purchase/add_purchase_order';
$route['purchase/order/detail/(:any)'] = 'transaction/Purchase/detail_purchase_order/$1';
$route['purchase/order/update/(:any)'] = 'transaction/Purchase/update_purchase_order/$1';
$route['purchase/order/print/(:any)'] = 'transaction/Purchase/print_purchase_order/$1';
// Transaction -> Purchase Invoice
$route['purchase/invoice'] = 'transaction/Purchase/purchase_invoice';
$route['purchase/invoice/create'] = 'transaction/Purchase/create_purchase_invoice';
$route['purchase/invoice/detail/(:any)'] = 'transaction/Purchase/detail_purchase_invoice/$1';
$route['purchase/invoice/update/(:any)'] = 'transaction/Purchase/update_purchase_invoice/$1';
$route['purchase/invoice/print/(:any)'] = 'transaction/Purchase/print_purchase_invoice/$1';
// Transaction -> Purchase Return
$route['purchase/return'] = 'transaction/Purchase/purchase_return';
$route['purchase/return/create'] = 'transaction/Purchase/create_purchase_return';
$route['purchase/return/detail/(:any)'] = 'transaction/Purchase/detail_purchase_return/$1';
$route['purchase/return/update/(:any)'] = 'transaction/Purchase/update_purchase_return/$1';
$route['purchase/return/print/(:any)'] = 'transaction/Purchase/print_purchase_return/$1';

// Transaction -> Sales Order
$route['sales/order'] = 'transaction/Sales/sales_order';
$route['sales/order/add'] = 'transaction/Sales/add_sales_order';
$route['sales/order/detail/(:any)'] = 'transaction/Sales/detail_sales_order/$1';
$route['sales/order/update/(:any)'] = 'transaction/Sales/update_sales_order/$1';
$route['sales/order/print/(:any)'] = 'transaction/Sales/print_sales_order/$1';
$route['sales/order/taking'] = 'transaction/Sales/sales_order_taking';
$route['sales/order/taking/add'] = 'transaction/Sales/add_sales_order_taking';
$route['sales/order/taking/detail/(:any)'] = 'transaction/Sales/detail_sales_order_taking/$1';
$route['sales/order/taking/print/(:any)'] = 'transaction/Sales/print_sales_order_taking/$1';
// Transaction -> Sales -> Invoice
$route['sales/invoice'] = 'transaction/Sales/sales_invoice';
$route['sales/invoice/create'] = 'transaction/Sales/create_sales_invoice';
$route['sales/invoice/add/(:any)'] = 'transaction/Sales/add_sales_invoice/$1';
$route['sales/invoice/update/(:any)'] = 'transaction/Sales/update_sales_invoice/$1';
$route['sales/invoice/detail/(:any)'] = 'transaction/Sales/detail_sales_invoice/$1';
$route['sales/invoice/print/(:any)'] = 'transaction/Sales/print_sales_invoice/$1';
$route['sales/invoice/print_do/(:any)'] = 'transaction/Sales/print_delivery_order/$1';
// Transaction -> Sales -> Return
$route['sales/return'] = 'transaction/Sales/sales_return';
$route['sales/return/create'] = 'transaction/Sales/create_sales_return';
$route['sales/return/update/(:any)'] = 'transaction/Sales/update_sales_return/$1';
$route['sales/return/detail/(:any)'] = 'transaction/Sales/detail_sales_return/$1';
$route['sales/return/print/(:any)'] = 'transaction/Sales/print_sales_return/$1';
// Transaction -> Delivery
$route['delivery'] = 'transaction/Delivery/index';
$route['delivery/create'] = 'transaction/Delivery/create';;
$route['delivery/update/(:any)'] = 'transaction/Delivery/update/$1';
$route['delivery/detail/(:any)'] = 'transaction/Delivery/detail/$1';
$route['delivery/print/(:any)'] = 'transaction/Delivery/print/$1';
// Transaction -> Sales -> Billing
$route['sales/billing'] = 'transaction/Sales/sales_billing';
$route['sales/billing/create'] = 'transaction/Sales/create_sales_billing';;
$route['sales/billing/update/(:any)'] = 'transaction/Sales/update/$1';
$route['sales/billing/detail/(:any)'] = 'transaction/Sales/detail_sales_billing/$1';
$route['sales/billing/print/(:any)'] = 'transaction/Sales/print_sales_billing/$1';

// POS -> Cashier
$route['pos/cashier'] = 'pos/Cashier';
$route['pos/cashier/open'] = 'pos/Cashier/open';
$route['pos/cashier/result/(:any)'] = 'pos/Cashier/result/$1';
$route['pos/cashier/print_bill/(:any)'] = 'pos/Cashier/print_bill/$1';
$route['pos/cashier/print_collect/(:any)'] = 'pos/Cashier/print_collect/$1';
$route['pos/cashier/summary/(:any)'] = 'pos/Cashier/summary/$1';
$route['pos/cashier/summary/print/(:any)'] = 'pos/Cashier/print_summary/$1';
// POS -> Transaction
$route['pos/transaction'] = 'pos/Transaction';
$route['pos/transaction/detail/(:any)'] = 'pos/Transaction/detail_transaction/$1';
$route['pos/transaction/update/(:any)'] = 'pos/Transaction/update_transaction/$1';
// POS -> Promotion
$route['pos/promotion'] = 'pos/Promotion';
$route['pos/promotion/add/'] = 'pos/Promotion/add';
$route['pos/promotion/detail/(:any)'] = 'pos/Promotion/detail/$1';

// Inventory -> Mutation
$route['mutation'] = 'inventory/Mutation';
$route['mutation/create'] = 'inventory/Mutation/create';
$route['mutation/detail/(:any)'] = 'inventory/Mutation/detail/$1';
$route['mutation/update/(:any)'] = 'inventory/Mutation/update/$1';
// Inventory -> Repacking
$route['repacking'] = 'inventory/Repacking';
$route['repacking/create'] = 'inventory/Repacking/create_repacking';
$route['repacking/detail/(:any)'] = 'inventory/Repacking/detail_repacking/$1';
// Stock -> Stock Card
$route['stock/card'] = 'transaction/Stock/stock_card';
// Stock -> Production
$route['stock/production'] = 'transaction/Stock/production';
$route['stock/production/add/(:any)'] = 'transaction/Stock/add_production/$1';
$route['stock/production/detail/(:any)'] = 'transaction/Stock/detail_production/$1';    
// Stock -> Opname & Adjusment
$route['opname'] = 'inventory/Opname';
$route['opname/create'] = 'inventory/Opname/create_stock_opname';
$route['opname/detail/(:any)'] = 'inventory/Opname/detail_stock_opname/$1';
$route['opname/print/(:any)'] = 'inventory/Opname/print_stock_opname/$1';
$route['opname/update/(:any)'] = 'inventory/Opname/update/$1';
$route['opname/adjusment/create/(:any)'] = 'inventory/Opname/create_adjusment_stock/$1';
// Inventory -> Repacking
$route['product_usage'] = 'inventory/Product_usage';
$route['product_usage/create'] = 'inventory/Product_usage/create_product_usage';
$route['product_usage/detail/(:any)'] = 'inventory/Product_usage/detail_product_usage/$1';

// Finance -> Cash Ledger
$route['cash_ledger/cash'] = 'finance/Cash_ledger/cash';
$route['cash_ledger/cash/account'] = 'finance/Cash_ledger/cash_account';
$route['cash_ledger/bank'] = 'finance/Cash_ledger/bank';
$route['cash_ledger/bank/account'] = 'finance/Cash_ledger/bank_account';
$route['cash_ledger/in_out'] = 'finance/Cash_ledger/cash_ledger_in_out';
$route['cash_ledger/in_out/create'] = 'finance/Cash_ledger/create_cash_ledger_in_out';
$route['cash_ledger/in_out/detail/(:any)'] = 'finance/Cash_ledger/detail_cash_ledger_in_out/$1';
$route['cash_ledger/mutation'] = 'finance/Cash_ledger/cash_ledger_mutation';
$route['cash_ledger/mutation/create'] = 'finance/Cash_ledger/create_cash_ledger_mutation';
$route['cash_ledger/mutation/detail/(:any)'] = 'finance/Cash_ledger/detail_cash_ledger_mutation/$1';    
$route['cash_ledger/supplier_deposit'] = 'finance/Cash_ledger/supplier_deposit';
$route['cash_ledger/customer_deposit'] = 'finance/Cash_ledger/customer_deposit';
// Finance -> Payment
$route['payment/debt'] = 'finance/Payment/payment_of_debt';
$route['payment/debt/create'] = 'finance/Payment/create_payment_of_debt';
$route['payment/debt/create/(:any)'] = 'finance/Payment/create_payment_of_debt/$1';
$route['payment/debt/detail/(:any)'] = 'finance/Payment/detail_payment_of_debt/$1';
// $route['payment/debt/update/(:any)'] = 'finance/Payment/update_payment_of_debt/$1';
$route['payment/receivable'] = 'finance/Payment/payment_of_receivable';
$route['payment/receivable/create'] = 'finance/Payment/create_payment_of_receivable';
$route['payment/receivable/create/(:any)'] = 'finance/Payment/create_payment_of_receivable/$1';
$route['payment/receivable/detail/(:any)'] = 'finance/Payment/detail_payment_of_receivable/$1';
// Finance -> Expense
$route['finance/expense'] = 'finance/Expense';
// Finance -> General Ledger
$route['general_ledger'] = 'finance/Accounting/general_ledger';
// Finance -> COA Account
$route['coa_account'] = 'finance/Accounting/coa_account';
// Finance -> Journal
$route['journal'] = 'finance/Accounting/journal';
$route['journal/create'] = 'finance/Accounting/create_journal';
$route['journal/detail/(:any)'] = 'finance/Accounting/detail_journal/$1';    

// Report -> report
$route['report'] = 'report/Report';
$route['report/purchase/(:any)'] = 'report/Purchase_report/$1';
$route['report/sales/(:any)'] = 'report/Sales_report/$1';
$route['report/finance/(:any)'] = 'report/Finance_report/$1';
$route['report/stock/(:any)'] = 'report/Stock_report/$1';
$route['report/inventory/(:any)'] = 'report/Inventory_report/$1';
$route['report/chart/(:any)'] = 'report/Chart_report/$1';
// Report -> Finance
$route['report/finance/sales/profit/view_detail/(:any)'] = 'report/Finance_report/view_sales_profit_detail_report/$1';

// Setting -> Profile
$route['setting/profile'] = 'setting/Profile';
// Setting -> User
$route['setting'] = 'setting/User';
$route['setting/user/create'] = 'setting/User/create';
$route['setting/user/detail/(:any)'] = 'setting/User/detail/$1';
$route['user/change_password/(:any)'] = 'setting/User/change_password/$1';