import './style.css'
import javascriptLogo from './javascript.svg'
import viteLogo from '/vite.svg'
import { setupCounter } from './counter.js'

document.querySelector('#app').innerHTML = `
  <div>
    <a href="https://vitejs.dev" target="_blank">
      <img src="${viteLogo}" class="logo" alt="Vite logo" />
    </a>
    <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript" target="_blank">
      <img src="${javascriptLogo}" class="logo vanilla" alt="JavaScript logo" />
    </a>
    <h1>Cabinet Juridique Excellence</h1>
    
    <section class="about">
      <h2>À propos de nous</h2>
      <p>
        Le Cabinet Juridique Excellence est reconnu pour son expertise approfondie et son engagement 
        envers l'excellence dans tous les aspects du droit. Notre équipe d'avocats expérimentés 
        vous accompagne avec professionnalisme et détermination.
      </p>
    </section>

    <section class="values">
      <h2>Nos Valeurs</h2>
      <div class="values-grid">
        <div class="value-card">
          <h3>Excellence</h3>
          <p>Nous visons l'excellence dans chaque dossier, avec une attention méticuleuse aux détails et une expertise approfondie.</p>
        </div>
        <div class="value-card">
          <h3>Intégrité</h3>
          <p>Notre engagement envers l'éthique et la transparence guide chacune de nos actions professionnelles.</p>
        </div>
        <div class="value-card">
          <h3>Confidentialité</h3>
          <p>La protection de la vie privée de nos clients est au cœur de notre approche professionnelle.</p>
        </div>
        <div class="value-card">
          <h3>Accessibilité</h3>
          <p>Nous rendons le droit accessible avec des explications claires et une approche client centrée.</p>
        </div>
      </div>
    </section>

    <div class="card">
      <button id="counter" type="button"></button>
    </div>
    
    <p class="read-the-docs">
      Contactez-nous pour une consultation personnalisée
    </p>
  </div>
`

setupCounter(document.querySelector('#counter'))
