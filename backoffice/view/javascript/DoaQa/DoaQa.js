$(document).ready(function () {
    jQuery( "#DqaQa_title_dropdown" ).change(function() {
      var id =$(this).val();
      var DoaQa_raw_data =$('#DoaQa_raw_data').val();
        DoaQa_raw_data = jQuery.parseJSON(DoaQa_raw_data);
        $('#DqaQa_language_dropdown').html('');
        $(DoaQa_raw_data).each(function(i) {

              $(this.Languages).each(function () {
                      if(this.id=id){
                      $(this.languages).each(function () {

                          console.log(this);
                          $('#DqaQa_language_dropdown').append("<option vlaue='"+this.Id+"'>"+this.Name+"</option>");});}
                });


        });
$('#DqaQa_language_dropdown').change(function () {
    var language = $(this).val();
    var languageId = $(this).text();
    var title = $("#DqaQa_title_dropdown").val();
    var titleId = $("#DqaQa_title_dropdown").text();
})
    });});