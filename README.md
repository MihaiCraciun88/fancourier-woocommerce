# fancourier-woocommerce
A refactored version of FAN Courier module for WooCommerce 3.x

![alt text](https://i.imgur.com/gehl4YV.png)
![alt text](https://i.imgur.com/VTaiD1z.png)

## Security notice!
Please note this module dosen't use SSL for cURL require. Using this, you might have a data leak.
If you want to fix this: https://stackoverflow.com/questions/9774349/php-curl-not-working-with-https

# Changelog
## 1.3.5
calculate_shipping trigger for phone number
don't send Company Name to FanCourier when select "Persoana Fizica" at checkout, using Facturare WooCommerce module
https://wordpress.org/plugins/facturare-persoana-fizica-sau-juridica/

## 1.3.0
refactored code from original module
removed boilerplate code
adds admin check for AWB debugging link
change AWB debug link style
fix admin shipping label 
fix frontend shipping label
adds AWB ID as meta for shipping
