/**
 * Created by Tim on 09/02/2016.
 */



    /**
     var couleurs = [
     {
         nom: 'Couleur Liberty',
         description: 'La couleur Liberty est incroyable',
         categorie: 'liberty',
         id: 1,
         disponible: true,
         principal: 	'img/tissu/tissu-liberty-princ.png',
         secondaire: 'img/tissu/tissu-liberty-sec.png',
         milieu: 	'img/tissu/tissu-liberty-milieu.png',
         thumb: 		'img/tissu/tissu-liberty-thumb.png'

     },
     {
         nom: 'Couleur Rouge',
         description: 'La couleur Rouge est jolie',
         categorie: 'monochrome',
         id: 2,
         disponible: true,
         imgName: 'rouge',
         principal: 	'img/tissu/tissu-rouge-princ.png',
         secondaire:	'img/tissu/tissu-rouge-sec.png',
         milieu: 	'img/tissu/tissu-rouge-milieu.png',
         thumb: 		'img/tissu/tissu-rouge-thumb.png'

     },
     {
         nom: 'Couleur Bleu',
         description: 'La couleur Bleu est jolie',
         categorie: 'monochrome',
         id: 3,
         disponible: true,
         imgName: 'bleu',
         principal: 	'img/tissu/tissu-bleu-princ.png',
         secondaire:	'img/tissu/tissu-bleu-sec.png',
         milieu: 	'img/tissu/tissu-bleu-milieu.png',
         thumb: 		'img/tissu/tissu-bleu-thumb.png'

     },
     {
         nom: 'Couleur Vert',
         description: 'La couleur Vert est jolie',
         categorie: 'monochrome',
         id: 4,
         disponible: true,
         imgName: 'vert',
         principal: 	'img/tissu/tissu-vert-princ.png',
         secondaire:	'img/tissu/tissu-vert-sec.png',
         milieu: 	'img/tissu/tissu-vert-milieu.png',
         thumb: 		'img/tissu/tissu-vert-thumb.png'

     }
     ];
     */


/**
 * 	var couleurs = [
 {
     nom: 'Couleur Liberty',
     description: 'La couleur Liberty est incroyable',
     categorie: 'liberty',
     id: 1,
     disponible: true,
     principal: 	'tissu-liberty-princ.png',
     secondaire: 'tissu-liberty-sec.png',
     milieu: 	'tissu-liberty-milieu.png',
     thumb: 		'tissu-liberty-thumb.png'

 },
 {
     nom: 'Couleur Rouge',
     description: 'La couleur Rouge est jolie',
     categorie: 'monochrome',
     id: 2,
     disponible: true,
     principal: 	'tissu-rouge-princ.png',
     secondaire:	'tissu-rouge-sec.png',
     milieu: 	'tissu-rouge-milieu.png',
     thumb: 		'tissu-rouge-thumb.png'

 },
 {
     nom: 'Couleur Bleu',
     description: 'La couleur Bleu est jolie',
     categorie: 'monochrome',
     id: 3,
     disponible: true,
     principal: 	'tissu-bleu-princ.png',
     secondaire:	'tissu-bleu-sec.png',
     milieu: 	'tissu-bleu-milieu.png',
     thumb: 		'tissu-bleu-thumb.png'

 },
 {
     nom: 'Couleur Vert',
     description: 'La couleur Vert est jolie',
     categorie: 'monochrome',
     id: 4,
     disponible: true,
     principal: 	'tissu-vert-princ.png',
     secondaire:	'tissu-vert-sec.png',
     milieu: 	'tissu-vert-milieu.png',
     thumb: 		'tissu-vert-thumb.png'

 }
 ];
 *
 */

var app = angular.module('myApp', []);
    app.controller('customersCtrl', function($scope, $http) {
        $http.get("http://127.0.0.1/edsa-template-php/couleurs.php")
            .then(function (response) {$scope.couleurs = response.data.couleurs;});
    });
