//On import flipper et spring depuis flip-toolkit
import { Flipper, spring } from 'flip-toolkit'




/**
 * @property {HTMLElement} pagination
 * @property {HTMLElement} content
 * @property {HTMLElement} sorting
 * @property {HTMLFormElement} form
 * @property {boolean} moreNav
 */
export default class Filter {
    //   * @property {number} page

    /** On Précise que l'element racine sera de type html ou nul
     *  @param {HTMLElement|null} element 
     */
    constructor(element) {
        if (element === null) {
            return
        }
        // On balise nos élements 
        this.pagination = element.querySelector('.js-filter-pagination')
        this.content = element.querySelector('.js-filter-content')
        this.sorting = element.querySelector('.js-filter-sorting')
        this.form = element.querySelector('.js-filter-form')
            // this.page = parseInt(new URLSearchParams(window.location.search).get('page') || 1)
            // this.moreNav = this.page === 1
        this.bindEvents()
    }

    /**
     * Ajoute les comportements aux différents éléments
     */
    bindEvents() {
        //On prend en pramatere l'evenement
        const aClickListener = e => {
                // si l'element cliqué est un lien
                if (e.target.tagName === 'A') {
                    // on enleve le comportement par defaut
                    e.preventDefault()
                        //On charge l'url au niveau du lien 
                    this.loadUrl(e.target.getAttribute('href'))
                }

            }
            // Au click sur un element à l'interieur de sorting
        this.sorting.addEventListener('click', e => {
            aClickListener(e)
            this.page = 1
        })
        if (this.moreNav) {
            // this.pagination.innerHTML = '<button class="btn btn-primary">Voir plus</button>'
            // this.pagination.querySelector('button').addEventListener('click', this.loadMore.bind(this))
        } else {
            this.pagination.addEventListener('click', aClickListener)
        }
        // Au click sur un element à l'interieur de la pagination
        this.pagination.addEventListener('click', aClickListener)

        //On selectionne tous les champs input
        this.form.querySelectorAll('input').forEach(input => {
            //Pour chaque élément on ecoute si il y a un changement et on récupère l'evenement
            input.addEventListener('change', this.loadForm.bind(this))
        })
    }


    // async loadMore() {
    //     const button = this.pagination.querySelector('button')
    //     button.setAttribute('disabled', 'disabled')
    //     this.page++
    //         const url = new URL(window.location.href)
    //     const params = new URLSearchParams(url.search)
    //     params.set('page', this.page)
    //     await this.loadUrl(url.pathname + '?' + params.toString(), true)
    //     button.removeAttribute('disabled')
    // }

    //genere automatiquement l'url à partir des données du form
    async loadForm() {
        this.page = 1
            //On recupère les données du forms
        const data = new FormData(this.form)
            // on créé une url à partir de ce que l'on retrouve dans le formulaire et on récupère l'action du formulaire , et si l'action n'existe pas on récupère l'url courante
        const url = new URL(this.form.getAttribute('action') || window.location.href)
            //On genere les paramètres ( permet de consruire les paramètres d'url dynamiquement)
        const params = new URLSearchParams()
            //On parcours l'ensemble des données en recuperant la valeur et la clé
        data.forEach((value, key) => {
            //On rajoute pour la clé la valeur suivante
            params.append(key, value)
        });
        //on return que le chemin et recupère les paramètres convertis en chaine de caractère
        return this.loadUrl(url.pathname + '?' + params.toString())
    }

    async loadUrl(url, append = false) {
        //On fait apparaitre le loader
        this.showLoader()
            // enleve ajax de l'url pour , en cas de retour a la page précédente , d'afficher un tableau json
        const params = new URLSearchParams(url.split('?')[1] || '')
        params.set('ajax', 1) //
            //On attend le resultat du fetch
        const response = await fetch(url.split('?')[0] + '?' + params.toString(), { //
                //On explique a Symfony qu'on fait un appel en ajax
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            // Si on a un status qui est supp ou égal à 200 et que le status est strict inf à 300
        if (response.status >= 200 && response.status < 300) {
            //on recupère les données en json
            const data = await response.json()
                //On renvoie les contenus de notre reponse
            this.flipContent(data.content, append)
            this.sorting.innerHTML = data.sorting
            if (!this.moreNav) {
                //on remplace la pagination
                this.pagination.innerHTML = data.pagination
            } else if (this.page === data.pages) {
                this.pagination.style.display = 'none';
            } else {
                this.pagination.style.display = null;
            }
            this.updatePrices(data)
                //on supprime la clé ajax dans l'url
            params.delete('ajax')
                //Si le traitement s'est bien deroulé on change l'url ( plus simple pour partager une page avec des recherches )
            history.replaceState({}, '', url.split('?')[0] + '?' + params.toString())
                // pushState pour revenir au filtre précédent au lieu de la page 
        } else {
            console.error(response)
        }
        //On fait disparaitre le loader
        this.hideLoader()
    }

    /**
     * Remplace les élements de la grille avec un effet d'animation flip
     * @param {string} content 
     */
    flipContent(content, append) {
        //Permet de changer la "force" du rebond d'animation
        const springConfig = 'gentle' // 'wobbly' 'veryGentle'
            //Fonction qui permet de gerer l'animation des produits sortant
        const exitSpring = function(element, index, complete) {
                spring({
                    config: 'stiff',
                    values: {

                        translateY: [0, -20],
                        opacity: [1, 0]
                    },
                    onUpdate: ({ translateY, opacity }) => {
                        element.style.opacity = opacity;
                        element.style.transform = `translateY(${translateY}px)`;
                    },
                    // delay: i * 25, délai
                    onComplete: complete
                        // add callback logic here if necessary

                })
            }
            //Fonction qui permet de gerer l'animation des produits entrant
        const appearSpring = function(element, index) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [20, 0],
                    opacity: [0, 1]
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                delay: index * 15
            })
        }
        const flipper = new Flipper({
                element: this.content
            })
            //On parcourt tous les enfants de this.content , et pour chaque enfant 
        this.content.children.forEach(element => {
                //On ajout la methode addFlipped
                flipper.addFlipped({
                    //on lui passe l'element
                    element,
                    //
                    spring: springConfig,
                    //permet d'identifier si c'est le meme élement sur la page suivante
                    flipId: element.id,
                    //on desactive l'animation pour ces element car ce sont les elements qui dont la avant le changement , ils disparaissent
                    shouldFlip: false,
                    //à la sortie 
                    onExit: exitSpring
                })
            })
            // On memorise la postion de tous les element renseigné
        flipper.recordBeforeUpdate()
        if (append) {
            this.content.innerHTML += content
        } else {

            this.content.innerHTML = content
        }
        this.content.children.forEach(element => {
            flipper.addFlipped({
                element,
                spring: springConfig,
                flipId: element.id,
                onAppear: appearSpring
            })
        })
        flipper.update()

    }

    //Barre de chargement 

    //Affiche le loader
    showLoader() {
        //On ajout une class a notre form
        this.form.classList.add('is-loading')
            //On cherche dans le form l'elemnt qui a la class js-loading
        const loader = this.form.querySelector('.js-loading')
            //Si ça n'existe pas
        if (loader === null) {
            return
        }
        //Si il existe on le fait apparaitre 
        loader.setAttribute('aria-hidden', 'false')
        loader.style.display = null;
    }

    //Cache le loader
    hideLoader() {
        this.form.classList.remove('is-loading')
        const loader = this.form.querySelector('.js-loading')
        if (loader === null) {
            return
        }
        loader.setAttribute('aria-hidden', 'true')
        loader.style.display = 'none';
    }

    updatePrices({ min, max }) {
        const slider = document.getElementById('price-slider')
        if (slider === null) {
            return
        }

        slider.noUiSlider.updateOptions({
            range: {
                min: [min],
                max: [max]
            }
        })
    }
}