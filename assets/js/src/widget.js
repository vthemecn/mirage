/**
 * 小工具
 */

function sideMenuInit(){
	var btns = document.querySelectorAll('.side-menu .arrow');
	if(btns.length){
		btns.forEach(btn=>{
			btn.addEventListener('click', e => {
				console.log('e', e);
				e.preventDefault();
          // if(!e.target.classList.contains('arrow')){ return; }
				e.target.parentNode.parentNode.classList.toggle('hide-children');
				return;
			});
		});
	}
}


export default { sideMenuInit };
