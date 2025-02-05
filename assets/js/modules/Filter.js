import { Flipper, spring } from 'flip-toolkit'

/**
 * @property {HTMLElement} pagination
 * @property {HTMLElement} content
 * @property {HTMLFormElement} form
 */
export default class Filter {
    /**
     * @param {HTMLElement|null} element 
     */
    constructor(element) {
        if (element === null) {
            return
        }
        this.pagination = element.querySelector('.js-filter-pagination')
        this.content = element.querySelector('.js-filter-content')
        this.form = element.querySelector('.js-filter-form')
        this.button = element.querySelector('#submit-filter')
        this.bindEvents()
    }

    /**
     * Ajoute les comportements aux différents éléments
     */
    bindEvents() {
        const aClickListener = e => {
            if (e.target.tagName === 'A') {
                e.preventDefault()
                this.loadUrl(e.target.getAttribute('href'))
            }
        }
        this.pagination.addEventListener('click', aClickListener)
        // Sélection initiale du premier input radio
        const firstRadio = this.form.querySelector('input[type=radio]:checked')
        if (firstRadio) {
            firstRadio.parentElement.classList.add('vxvpze')
        }
        this.form.querySelectorAll('input[type=radio]').forEach(input => {
            input.addEventListener('change', (event) => {
                // Supprime la classe des autres inputs
                this.form.querySelectorAll('input[type=radio]').forEach(radio => {
                    radio.parentElement.classList.remove('vxvpze')
                })
                // Ajoute la class CSS au parent du radio sélectionné
                event.target.parentElement.classList.add('vxvpze')
                this.loadForm()
            })
        })
        this.form.querySelector('#q').addEventListener('keyup', this.loadForm.bind(this))
        this.button.style.display = 'none'
    }

    async loadForm() {
        const data = new FormData(this.form)
        const url = new URL(this.form.getAttribute('action') || window.location.href)
        const params = new URLSearchParams()
        data.forEach((value, key) => {
            params.append(key, value)
        })
        return this.loadUrl(url.pathname + '?' + params.toString())
    }

    async loadUrl(url) {
        this.showLoader()
        const params = new URLSearchParams(url.split('?')[1] || '')
        params.set('ajax', 1)
        const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json()
            this.flipContent(data.content)
            // this.content.innerHTML = data.content
            this.pagination.innerHTML = data.pagination
            params.delete('ajax') // On ne veut pas que le paramètre "ajax" se retrouve dans l'URL
            history.replaceState({}, '', url.split('?')[0] + '?' + params.toString())
        } else {
            console.error(response)
        }
        this.hideLoader()
    }

    showLoader() {
        this.form.classList.add('is-loading')
        const loader = this.form.querySelector('.js-loading')
        if (loader === null) {
            return
        }
        loader.setAttribute('aria-hidden', 'false')
        loader.style.display = null
    }

    hideLoader() {
        this.form.classList.remove('is-loading')
        const loader = this.form.querySelector('.js-loading')
        if (loader === null) {
            return
        }
        loader.setAttribute('aria-hidden', 'true')
        loader.style.display = 'none'
    }

    /**
     * Remplace les éléments de la grille avec un effet d'animation FLIP
     * @param {string} content 
     */
    flipContent(content) {
        const springConfig = 'gentle'
        const exitSpring = function (element, index, onComplete) {
            spring({
                config: springConfig,
                values: {
                    translateY: [0, -20],
                    opacity: [1, 0]
                  },
                onUpdate: ({ 
                    translateY, 
                    opacity 
                }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                onComplete
            })
        }
        const appearSpring = function (element, index) {
            spring({
                config: springConfig,
                values: {
                    translateY: [20, 0],
                    opacity: [0, 1]
                  },
                onUpdate: ({ 
                    translateY, 
                    opacity 
                }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                delay: index * 3,
            })
        }

        const flipper = new Flipper({
            element: this.content
        })
        Array.from(this.content.children).forEach(element => {
            flipper.addFlipped({
                element,
                spring: springConfig,
                flipId: element.id,
                shouldFlip: false,
                onExit: exitSpring
            })
        })
        flipper.recordBeforeUpdate()
        this.content.innerHTML = content
        Array.from(this.content.children).forEach(element => {
            flipper.addFlipped({
                element,
                spring: springConfig,
                flipId: element.id,
                onAppear: appearSpring
            })
        })
        flipper.update()
    }
}