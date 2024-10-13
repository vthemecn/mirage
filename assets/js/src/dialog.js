/**
 * dialogTools
 */

function registerDialog(selector){
  selector.show = show;
  selector.showModal = showModal;
  selector.close = close;

  closeButtonInit.apply(selector);
}

function show() {
  this.setAttribute('open','');
}

function showModal() {
  document.body.classList.add('no-scroll');
  this.setAttribute('modal','');
  this.setAttribute('open','');
}

function close() {
  document.body.classList.remove('no-scroll');
  this.removeAttribute('modal');
  this.removeAttribute('open');
}

function closeButtonInit() {
  var closeButtons = this.querySelectorAll('.close');
  closeButtons.forEach(btn => {
    btn.addEventListener('click', e => {
      this.close();
    });
  });
}

export default { registerDialog };
