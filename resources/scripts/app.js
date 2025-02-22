import domReady from '@roots/sage/client/dom-ready';
import { toogler, getParentNode, stilist } from "./helpers.js"
import { customMenu } from "./custom-menu"
/**
 * Application entrypoint
 */
domReady(async () => {

  await customMenu()

  const showPcMenuButton = document.getElementById('show-menu-pc')
  const NavPc = document.getElementById('nav-pc')
  const NavMobile = document.getElementById('nav-mobile')
  const menuBgElement = document.querySelector("[data-qaid='menu-bg']")

  showPcMenuButton.addEventListener('click', function() {
    console.log("showPcMenuButton click", )
    if(NavPc){
      toogler(menuBgElement)
      toogler(NavPc)
    }
    if(NavMobile){
      toogler(menuBgElement)
      toogler(NavMobile)
    }
  });
});

(()=>
  {
      console.log("%c%s", "color: red; background: yellow; font-size: 62px;", "НЕБЕЗПЕЧНА ЗОНА ФРОНТЕНД!")
      console.log("%c%s", "color: red; background: yellow; font-size: 62px;", "DANGER ZONE FRONTEND!")
      console.log("%c made with 🩷 by https://alexturik.com", "color:#7f6df2; font-size:21x; font-weight:bold;");
  }
)()
/**
 * @see {@link https://webpack.js.org/api/hot-module-replacement/}
 */
if (import.meta.webpackHot) import.meta.webpackHot.accept(console.error);
