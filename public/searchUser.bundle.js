document.addEventListener("DOMContentLoaded",(function(){const e=document.getElementById("keyword");e.focus(),n(""),document.getElementById("search_delete").addEventListener("click",(function(t){e.value="",n("")})),e.addEventListener("input",(function(e){n(e.target.value)}));let t=document.getElementById("users_container");function n(e){fetch(`/search/user-list?keyword=${e}`,{method:"GET"}).then((e=>e.json())).then((e=>{"success"===e.status?t.innerHTML=e.htmlString:"error"===e.status&&alert(e.message)})).catch((e=>{alert("エラーが発生しました。更新してみてください")}))}}));