import '../styles/app.scss';
import $ from 'jquery';
import greet from '../greet';
import 'bootstrap';

// const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

$(document).ready(function() {
    $('body').prepend('<h1>'+greet()+'</h1>');
    $('[data-toggle="popover"]').popover();
});