
/**
* Theme: Ubold Admin Template
* Author: Coderthemes
* SweetAlert
*/

!function ($) {
    "use strict";

    var SweetAlert = function () {
    };

    //examples
    SweetAlert.prototype.init = function () {

        //Basic
        $('#sa-basic').click(function () {
            swal("Here's a message!");
        });

        //A title with a text under
        $('#sa-title').click(function () {
            swal("Here's a message!", "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lorem erat, tincidunt vitae ipsum et, pellentesque maximus enim. Mauris eleifend ex semper, lobortis purus sed, pharetra felis")
        });

        //Success Message
        $('#sa-success').click(function () {
            swal("Good job!", "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lorem erat, tincidunt vitae ipsum et, pellentesque maximus enim. Mauris eleifend ex semper, lobortis purus sed, pharetra felis", "success")
        });

        //Warning Message
        $('#sa-warning').click(function () {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this imaginary file!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn-success',
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            }, function () {
                swal("Deleted!", "Your imaginary file has been deleted.", "success");
            });
        });

        //Parameter
        $('#sa-params').click(function () {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this imaginary file!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel plx!",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function (isConfirm) {
                if (isConfirm) {
                    swal("Deleted!", "Your imaginary file has been deleted.", "success");
                } else {
                    swal("Cancelled", "Your imaginary file is safe :)", "error");
                }
            });
        });

        //Custom Image
        $('#sa-image').click(function () {
            swal({
                title: "Sweet!",
                text: "Here's a custom image.",
                imageUrl: "assets/plugins/bootstrap-sweetalert/thumbs-up.jpg"
            });
        });

        //Auto Close Timer
        $('#sa-close').click(function () {
            swal({
                title: "Auto close alert!",
                text: "I will close in 2 seconds.",
                timer: 2000,
                showConfirmButton: false
            });
        });

        //Primary
        $('#primary-alert').click(function () {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this imaginary file!",
                type: "info",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-primary btn-sm waves-effect waves-light',
                confirmButtonText: 'Primary!'
            });
        });

        //Info
        $('#info-alert').click(function () {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this imaginary file!",
                type: "info",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-info btn-sm waves-effect waves-light',
                confirmButtonText: 'Info!'
            });
        });

        //Success
        $('#success-alert').click(function () {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this imaginary file!",
                type: "success",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm',
                confirmButtonText: 'Success!'
            });
        });

        //Warning
        $('#warning-alert').click(function () {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this imaginary file!",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm',
                confirmButtonText: 'Warning!'
            });
        });

        //Danger
        $('#danger-alert').click(function () {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this imaginary file!",
                type: "error",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm waves-effect waves-light',
                confirmButtonText: 'Danger!'
            });
        });

        //Delete page
        $('#warning-alert-delete-page').click(function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            swal({
                title: "Êtes-vous sûr ?",
                text: "Voulez-vous vraiment supprimer cette page ? cette page sera définitivement supprimée. cette action est irréversible.",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm',
                cancelButtonText: "Annuler",
                confirmButtonText: 'Supprimer'
            },
                 function(){
                window.location.href = link;
            });
        });
        
        //Delete page
        $('.warning-alert-delete-post').click(function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            swal({
                title: "Êtes-vous sûr ?",
                text: "Voulez-vous vraiment supprimer ce post ?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm',
                cancelButtonText: "Annuler",
                confirmButtonText: 'Supprimer'
            },
                 function(){
                window.location.href = link;
            });
        });

        //Delete photos
        $('.warning-alert-delete-photos').click(function(e){
            e.preventDefault();
            swal({
                title: "Êtes-vous sûr ?",
                text: "Voulez-vous vraiment supprimer les photos sélectionnées ?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm',
                cancelButtonText: "Annuler",
                confirmButtonText: 'Supprimer',
                closeOnConfirm: false
            },
                 function(){
                window.location.href = link;
            });
        });

        $('.delete-photo').click(function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            swal({
                title: "Êtes-vous sûr ?",
                text: "Voulez-vous vraiment supprimer cette photo ?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm',
                cancelButtonText: "Annuler",
                confirmButtonText: 'Supprimer'
            },
                 function(){
                window.location.href = link;
            });
        });

        //Commentaires
        $('.warning-alert-delete-comment').click(function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            swal({
                title: "Êtes-vous sûr ?",
                text: "Voulez-vous vraiment supprimer ce commentaire ?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm',
                cancelButtonText: "Annuler",
                confirmButtonText: 'Supprimer',
                closeOnConfirm: false
            },
                 function(){
                window.location.href = link;

            });
        });

        $('.warning-alert-delete').click(function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            swal({
                title: "Êtes-vous sûr ?",
                text: "Voulez-vous vraiment continuer ?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm',
                cancelButtonText: "Annuler",
                confirmButtonText: 'Continuer',
                closeOnConfirm: false
            },
                 function(){
                window.location.href = link;

            });
        });

        $('.warning-alert-wait-comment').click(function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            swal({
                title: "Êtes-vous sûr ?",
                text: "Êtes-vous sûr de vouloir mettre ce commentaire en attente ?\n Il ne sera pas visible sur votre book.",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm',
                cancelButtonText: "Annuler",
                confirmButtonText: 'Continuer',
                closeOnConfirm: false
            },
                 function(){
                window.location.href = link;
                //swal("Cool!", "Commentaire supprimé avec succès", "success");
                /*setTimeout(function(){
                    window.location.href = link;
                }, 3000);*/

            });
        });

        //Album
        $('.warning-alert-delete-folder').click(function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            swal({
                title: "Êtes-vous sûr ?",
                text: "Voulez-vous vraiment supprimer cette galerie ?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm',
                cancelButtonText: "Annuler",
                confirmButtonText: 'Supprimer',
                closeOnConfirm: false
            },
                 function(){
                window.location.href = link;
            });
        });

        //Album
        $('.warning-alert-delete-video').click(function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            swal({
                title: "Êtes-vous sûr ?",
                text: "Voulez-vous vraiment supprimer cette vidéo ?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm',
                cancelButtonText: "Annuler",
                confirmButtonText: 'Supprimer',
                closeOnConfirm: false
            },
                 function(){
                window.location.href = link;
            });
        });

        //Commentaires
        $('#warning-alert-delete-relation').click(function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            swal({
                title: "Êtes-vous sûr ?",
                text: "Voulez-vous vraiment retirer cet artiste de vos contacts ?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm',
                cancelButtonText: "Annuler",
                confirmButtonText: 'Retirer',
                closeOnConfirm: false
            },
                 function(){
                window.location.href = link;

            });
        });

        //Commentaires
        $('.warning-alert-simple').click(function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            swal({
                title: "Êtes-vous sûr ?",
                text: "Voulez-vous vraiment continuer ?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm',
                cancelButtonText: "Annuler",
                confirmButtonText: 'Continuer',
                closeOnConfirm: false
            },
                 function(){
                window.location.href = link;

            });
        });



        //Delete account
        /*$('#danger-alert-delete-account').click(function(e){
            e.preventDefault();
            swal({
                title: "Êtes-vous sûr ?",
                text: "Voulez-vous vraiment supprimer votre book ?",
                type: "danger",
                showCancelButton: true,
                cancelButtonClass: 'btn-light btn-sm waves-effect',
                confirmButtonClass: 'btn-danger btn-sm waves-effect waves-light',
                cancelButtonText: "Annuler",
                confirmButtonText: 'Supprimer définitivement',
                closeOnConfirm: false
            },
                 function(){
                swal("Cool!", "Book supprimé ! Merci d'avoir participer à l'expérience Bookfolio", "success");
                setTimeout(function(){
                    $("form#delete-account-form").submit();
                }, 3000);

            });
        });*/


    },
        //init
        $.SweetAlert = new SweetAlert, $.SweetAlert.Constructor = SweetAlert
}(window.jQuery),

    //initializing
    function ($) {
    "use strict";
    $.SweetAlert.init()
}(window.jQuery);