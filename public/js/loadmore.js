document.addEventListener("DOMContentLoaded", () => {

  loadmoreButton = document.getElementById("loadmore");
  updateLink(loadmoreButton);
  //console.log(loadmoreButton);
  
  // When click on "Load more" button, load next page items in ajax just before the button
  loadmoreButton.addEventListener("click", function (event) {
    event.preventDefault();
    const el = this;
      axios.get(el.getAttribute('href')).then(function (response) {
      el.parentElement.insertAdjacentHTML('beforebegin', response.data);
      updateLink(el);
    });
  });

  // Check the html content of the previous element and update or remove load more button
  function updateLink(el) {
    let parent = el.parentElement;
    console.log(parent)
    let link = parent.previousElementSibling.innerHTML;
    if (link) {
      el.setAttribute('href', link);
    } else {
      parent.remove();
    }
  }
    //let nextTricks = document.getElementById("nextTricks");
    //console.log(nextTricks);
    //let link = nextTricks.innerHTML;
    // if (link) {
    //   el.setAttribute('href', link);
    // } else {
      //nextTricks.remove();
    
});