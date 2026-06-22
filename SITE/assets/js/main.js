const button = document.querySelector('#statusButton');
const statusText = document.querySelector('#statusText');

if (button && statusText) {
  button.addEventListener('click', () => {
    statusText.textContent = 'JavaScript is working. Ready for the next feature.';
  });
}
