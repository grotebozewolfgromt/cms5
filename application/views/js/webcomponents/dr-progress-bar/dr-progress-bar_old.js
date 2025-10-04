<?php 
/**
 * dr-progress-bar.js
 *
 * webcomponent for a progress bar
 * 
 * 
 * HTML ATTRIBUTES:
 * - <dr-progress-bar percent="65">     ==> OPTIONAL
 *  
 * EXAMPLE:
 *  <dr-progress-bar percent="10" id="progressid" onclick="document.getElementById('progressid').percent = document.getElementById('progressid').percent+10"></dr-progress-bar>
 * 
 * CSS VARIABLES:
 *      --lightmode-color-drprogressbar-fill: rgb(255, 0, 251);
        --lightmode-color-drprogressbar-background: rgb(252, 240, 13);
        --darkmode-color-drprogressbar-fill: rgb(98, 255, 0);
        --darkmode-color-drprogressbar-background: rgb(0, 255, 251);
 * 
 * @todo also infinite progress
 * 
 * @author Dennis Renirie
 * 
 * 13 mrt 2025 dr-progress-bar.js created
 */
?> 

class DRProgressBar extends HTMLElement 
{
  static sCSS = `
        :host {
            display: block;
            width: 250px;
            height: 40px;
            background: light-dark(var(--lightmode-color-drprogressbar-background, rgb(234, 234, 234)), var(--darkmode-color-drprogressbar-background, rgb(71, 71, 71)));
            border-radius: 4px;
            overflow: hidden;
        }

        .fill {
            width: 0%;
            height: 100%;
            background: light-dark(var(--lightmode-color-drprogressbar-fill, rgb(32, 158, 255)), var(--darkmode-color-drprogressbar-fill, rgb(32, 158, 255)));
            transition: width 0.25s;
        }
    `;

  static get observedAttributes() 
  {
    return ["percent"];
  }

  constructor() {
    super();

    this.attachShadow({ mode: "open" });
    

    const objStyle = document.createElement("style");
    const objDIVFill = document.createElement("div");

    objStyle.innerHTML = DRProgressBar.sCSS;
    objDIVFill.classList.add("fill");

    this.shadowRoot.append(objStyle, objDIVFill);
  }

  get percent() 
  {
    const sValue = this.getAttribute("percent");

    if (isNaN(sValue)) 
      return 0;

    if (sValue < 0) 
      return 0;
  
    if (sValue > 100)
      return 100;

    return Number(sValue);
  }

  set percent(sValue)
  {
    this.setAttribute("percent", sValue);
  }

  attributeChangedCallback(name) 
  {
    if (name === "percent")
    {
        this.shadowRoot.querySelector(".fill").style.width = `${this.percent}%`;
    }
      // this.shadowRoot.querySelector(".fill").style.width = `${this.percent}%`;
  }
}

customElements.define("dr-progress-bar", DRProgressBar);
