# CI Baseplate

This is the starting I use for new web application projects, it takes codeigniter, adds useful libraries and customisations and eventually will include a build script for simple deployment.

The baseplate includes a simple example to demonstrate use of the menu library.

## Libraries Included

* Menu Library - from FuelCMS http://www.getfuelcms.com/user_guide/libraries/menu
* Sitemaps - helps with creation of xml sitemaps - https://github.com/chemicaloliver/codeigniter-sitemaps
* XML Writer - helps writing xml files
* MY_Cart - Simple cart mods (see comments)
* MY_Pagination - Allows extra bits and pieces to work (see comments)

## Unit Testing

Until PHPUnit support is finished in CI I will continue to use Codeigniter-Simpletest - https://github.com/ericbarnes/codeigniter-simpletest

## HTML5 Boilerplate

I've baked in most elements of the HTML5 boilerplate http://html5boilerplate.com/ to ease consistent support of HTML5 elements across browsers. 
