import { toogler, getParentNode, stilist } from "./helpers.js"

export async function customMenu(){

  document.addEventListener('click', (e)=>{
    const target = e.target
    //делегування на li, для випадків коли клік в дочірніх нодах тегу li
    const parentLiEl = getParentNode(target, 'li')
    //забираю лінку, якщо вона є в дата аргументах елементу.
    const parentLiElHasPath = parentLiEl?.dataset?.path
    //змінна в якій буде відповідь на запитання "а наш таргет події - це li, чи ні?" значенням 
    const isTargetLiEl = target.nodeName === "LI"
    /**
     * !змінна в якій буде  елемент li, незалежно від того, чи подія на ньому, чи на на його нодах
     * 
     * ! якщо елемент події це таг li 
     * ! ==> тоді в значення привязується елемент події
     * ! в у всіх інших випадки
     * ! ==> привязується parentLiEl в яку привязали значення заготовленої функції getParentNode
     */
    const targetLi = isTargetLiEl ? target : parentLiEl
    // змінна в якій буде відповідь на запитання "а наш таргет події має дата-атрибут data-mobile-menu?" 
    // ! як атрибут є, то його значення має бути true, бо я так реалізував в  php
    const targetLiDataMobileMenu = targetLi?.dataset?.mobileMenu

    // const isHasDataMobileMenu = isTargetLiEl 
    //       ? target?.dataset.mobileMenu
    //       : parentLiEl?.dataset.mobileMenu

    console.log('~~~~~~~~~~~~~~~target~~~~~~~~~~~~~~~~~~', target)
    console.log('~~~~~~~~~~~~~~~targetLi~~~~~~~~~~~~~~~~~~', targetLi)
    console.log('~~~~~~~~~~~~~~~targetLiDataMobileMenu~~~~~~~~~~~~~~~~~', targetLiDataMobileMenu)
    
    const path = targetLi?.dataset?.path ? targetLi.dataset.path : null
    const isParentElement = targetLi?.classList 
    ? [...targetLi?.classList].includes('without-parent') 
    : null
    && targetLi.dataset.path
    
    console.log('~~~~~~~~~~~~~~~path~~~~~~~~~~~~~~~~~', path)
    console.log('~~~~~~~~~~~~~~~isParentElement~~~~~~~~~~~~~~~~~', isParentElement)

    targetLiDataMobileMenu 
    ? (()=>{

      const isLiHasSubMenu = targetLi.querySelector('.submenu-wrapper') 
      const liSvgElement = targetLi.querySelector('svg')
      const isHasHidden = (el)=> el.classList('hidden')

      liSvgElement 
      ? stilist(liSvgElement, {
        rotate :liSvgElement.style.rotate === "90deg" 
        ? liSvgElement.style.rotate = "0deg" 
        : liSvgElement.style.rotate = "90deg"
      })
      : null
      console.log('~~~~~isLiHasSubMenu==>', isLiHasSubMenu)

      isLiHasSubMenu.classList.toggle('hidden')
      
      const hasChildren = targetLi.classList('menu-item-has-children')
      if(!hasChildren){
        window.location['href']=path
      }
    })()
    : (()=>{
      path ? window.location['href']=path : null
    })()
    // if(condition){
    //     window.location['href']=path
    //     console.log('path', path)
    //     console.log('window.location.href', window.location.href)
    // }
  })
} 