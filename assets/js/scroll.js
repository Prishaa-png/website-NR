function scrollToMiddle(event, id) {
  event.preventDefault(); // Stop default top-align jump
  const element = document.getElementById(id);
  if (element) {
    element.scrollIntoView({
      behavior: 'smooth',
      block: 'center' // Centers the element vertically
    });
  }
}