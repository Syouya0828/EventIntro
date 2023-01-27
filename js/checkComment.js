const post = document.querySelector('#commentBtn');
post.addEventListener('click', ()=> {
    var comment = document.getElementById('postComment').value;
    console.log(comment);
    if(comment==""){
        alert('コメントを入力してください');
        return;
    }
    const form = document.getElementById('comment-form');
    form.submit();
}, false);