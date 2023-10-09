/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/theme.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';
const $ = require('jquery');
global.$ = global.jQuery = $;

// start the Stimulus application
import './vendor/bootstrap';

import './js/app';


// Comment reply

//const commentWrite = document.querySelector('.md-comment-write');
//const commentTextArea = document.getElementById('comment_message');
//const commentReply = document.querySelectorAll('.comment-reply');
//const replyTo = document.getElementById('comment_replyTo');
//const replyFor = document.getElementById('comment_replyFor');
//const commentReplyUser = document.querySelector('.md-comment-reply-user');
//const replyingDelete = document.querySelector('.md-replying-delete');
//
//if (replyingDelete) {
//    replyingDelete.addEventListener('click', () => {
//        replyTo.removeAttribute('value');
//        replyFor.removeAttribute('value');
//        commentWrite.classList.remove('md-replying');
//    });
//}
//
//if (commentReply) {
//    commentReply.forEach((reply) => {
//        let replyUser = reply.querySelector('.reply-user');
//        let replyComment = reply.querySelector('.reply-comment');
//        reply.addEventListener('click', (reply) => {
//            replyTo.value = replyUser.innerHTML;
//            replyFor.value = replyComment.innerHTML;
//            commentTextArea.focus();
//
//            if (replyTo.value !== '') {
//                commentWrite.classList.add('md-replying');
//                commentReplyUser.innerHTML = replyTo.value;
//            }
//        })
//    });
//}
