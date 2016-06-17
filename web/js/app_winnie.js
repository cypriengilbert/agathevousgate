(function() {
	'use strict';
  
	// base angular module
	angular.module('noeudPap', [])
		.controller('noeudPapController', function($scope, $http){

			/**$http.get("http://127.0.0.1/edsa-template-php/couleurs.php").then(function (response) {
				$scope.couleurs = response.data.couleurs;
			});*/

			// Valeur par défaut de la variable couleur principale choisie
			this.col = 7;
			
			// Valeur par défaut de la variable couleur secondaire choisie
			this.colS = 8;
			
			// Valeur par défaut de la variable couleur du milieu choisie
			this.colM = 8;

			// Valeur par défaut de la variable couleur 4
			this.col4 = 2;

			// Valeur par défaut de la variable couleur 5
			this.col5 = 3;

			
			
			/* COULEUR PRINCIPALE */
			// Action lorsqu'un utilisateur clique sur une couleur
			this.selectColor = function(setColor){
				this.col = setColor;
			  };
	
			// Retourne true si la couleur en paramètre est sélectionnée
			this.isSelected = function(checkColor){
			  return this.col === checkColor;
			};
			
			
			/* COULEUR SECONDAIRE */
			this.selectColorS = function(setColorS){
				this.colS = setColorS;
			  };

			this.isSelectedS = function(checkColorS){
			  return this.colS === checkColorS;
			};
			
			
			/* COULEUR DU MILIEU */
			this.selectColorM = function(setColorM){
				this.colM = setColorM;
			  };

			this.isSelectedM = function(checkColorM){
			  return this.colM === checkColorM;
			};


			/* COULEUR 4 */
			this.selectColor4 = function(setColor4){
				this.col4 = setColor4;
			  };

			this.isSelected4 = function(checkColor4){
			  return this.col4 === checkColor4;
			};


			/* COULEUR 5 */
			this.selectColor5 = function(setColor5){
				this.col5 = setColor5;
			  };

			this.isSelected5 = function(checkColor5){
			  return this.col5 === checkColor5;
			};

			
			/* INVERSION COULEUR PRINCIPALE / SECONDAIRE */
			this.invertColor = function(){
				var colTemp = this.colS;
				this.colS = this.col;
				this.col = colTemp;
			};
	
		});

})();
