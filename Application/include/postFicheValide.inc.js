$(function() {

  // Tri des fiches en fonction de la date
  $('#envoyer').click(function() {

    var dateFiche = $('#dateFiche').val();
    var tabFiche = tabFihe;
    $.ajax({
      url : 'include/affichFiche.inc.php',
      type : 'POST',
      data : {'dateEnvoyer': dateFiche,
      'tabFiche': tabFiche
},


      success: function(data){
        $('#fiche').html(data);
      }
    });
  });




});
