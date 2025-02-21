
export function getParentNode(el, tagName) {
    if(!el || !tagName) return

    while (el && el.parentNode) {
      el = el.parentNode;
      
      if (el && el.tagName == tagName.toUpperCase()) {
        return el;
      }
    }
    
    return null;
  }

export function toogler(element) {
    if(!element) return
    element?.classList 
    ? element?.classList.toggle('hidden')
    : null
  }

export function stilist(element, styles = {}) {
    if(!element) return
	return Object.assign(element.style, styles)
}