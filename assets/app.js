/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './css/app.css';
import './css/navbar.css';


// start the Stimulus application
import $ from 'jquery';
import 'bootstrap';

document.addEventListener('DOMContentLoaded', () =>{
    new App();
});

$('.custom-file').on('change', function (e) {
    var inputFile = e.currentTarget;
    //$(inputFile).parent().find('custom-file-label').html(inputFile.files[0].name);
});

class App{
    /*constructor(){
        this.enableDropdowns();
        this.handleCommentForm();
    }

     enableDropdowns = () =>{
        const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
        dropdownElementList.map(function (dropdownToggleEL){
            return new Dropdown(dropdownToggleEL)
        });
    }*/


    //Manager le formulaire de commentaire
    handleCommentForm(){
        const commentForm = document.querySelector('form.comment-form');/**Depuis pin show */

        if (null === commentForm){
            return;
        }
        commentForm.addEventListener('submit', async(e)=>{
            e.preventDefault();

            const response = await fetch('/ajax/comment',/**URL(Route) pour envoyer la réponse */
            {
                method: 'POST',
                body: new FormData(e.target)/**l'objet qui a declanché l'évenement */
            });

            if (!response.ok){
                return;
            }
            const json = await response.json();

            if (json.code === 'COMMENT_ADDED_SUCCESSFULLY')
            {
                    const commentList = document.querySelector('.comment-list');
                    const commentCount = document.querySelector('.comment-count');
                    const commentContent = document.querySelector('.comment_content');
                    commentList.insertAdjacentHTML('beforeend', json.message);//beforeend
                    commentList.lastElementChild.scrollIntoView();//Faire scroller l'user jusqu'à son commentaire
                    commentCount.innerText = json.numberOfComments;
                    commentContent.value = '';//vider le textarea de comment après chaque submit
            }
        });      
    }
}