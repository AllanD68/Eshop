<?php

namespace App\Data;

//  Permet de représenter les données lié à la recherche 
class SearchData

{

    /**
     * // On recupère le numéro de la page
     * @var int
     */
    public $page = 1;


    /**
     * @var string
     * Permet de faire une recheche en rentrant des mots clés
     */
    public $q = '';


    // Permet de faire une recherche seulement avec les propriétés cochées 

    /**
     * @var Genre
     */
    public $genres = [];

    /**
     * @var Platform
     */
    public $platforms = [];

    /**
     * @var Conceptor
     */
    public $conceptor = [];

     /**
     * @var Category
     */
    public $category = [];

    
    /**
     * @var Review
     */
    public $reviews = [];

    /**
     * @var null|integer
     */
    public $max;

    /**
     * @var null|integer
     */
    public $min;

    /**
     * @var boolean
     */

     public $new = false;

}