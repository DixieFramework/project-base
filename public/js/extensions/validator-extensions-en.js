/**! compression tag for ftp-deployment */

/**
 * -------------- Validator messages extension --------------
 */
(function ($) {
    'use strict';

    $.fn.extend({
        /**
         * Returns the label text of the input element.
         *
         * @return {string|boolean} the label text, if found; false otherwise.
         */
        getLabelText: function () {
            const $element = $(this);
            const label = $element.attr('aria-label');
            if (label) {
                return label;
            }
            let $label = $element.siblings('.form-label');
            if ($label.length) {
                return $label.text();
            }
            const $parent = $element.parents('.form-group:first,.mb-3:first');
            if ($parent.length) {
                let $label = $parent.find('.form-label:first,label:first');
                if ($label.length) {
                    return $label.text();
                }
                $label = $parent.find('legend:first');
                if ($label.length) {
                    return $label.text();
                }
            }
            return false;
        }

    });

    /**
     * This is used to validate the Switzerland zip code (1000 - 9999).
     */
    $.validator.addMethod("zipcodeCH", function (value, element) {
        return this.optional(element) || /^[1-9]\d{3}$/.test(value);
    });

    $.validator.addMethod("notEqualToZero", function (value, element) {
        return this.optional(element) || $.parseFloat(value) !== 0;
    });

    $.extend($.validator, {
        /**
         * Format message within the label (if any).
         *
         * @param {HTMLElement} element - The element to search in.
         * @param {string} message - the message to use when label text is found.
         * @param {string} defaultMessage - the default message to use when label text is not found.
         * @param {any} [params] - the optional parameters to use for formatting the message.
         * @return {string} the formatted message.
         */
        formatLabel: function (element, message, defaultMessage, params) {
            // check parameters
            if ($.isUndefined(params)) {
                params = [];
            }
            if (arguments.length > 3 && params.constructor !== Array) {
                params = $.makeArray(arguments).slice(3);
            }
            if (params.constructor !== Array) {
                params = [params];
            }

            // check for date
            const $element = $(element);
            if ($element.attr('type') === 'date') {
                const regex = /^\d{4}-\d{2}-\d{2}$/;
                params = params.map(function (param) {
                    if (param.match(regex)) {
                        // let options = {year: "numeric", month: "long", day: "numeric"};
                        // return new Date(param).toLocaleDateString(undefined, options);
                        return new Date(param).toLocaleDateString();
                    }
                    return param;
                });
            }

            // get text
            const text = $element.getLabelText();
            if (text) {
                params.unshift(text);
                return $.validator.format(message, params);
            }
            return $.validator.format(defaultMessage, params);
        },

        /**
         * Translate the given size.
         *
         * @param {number} bytes - the size, in bytes, to translate.
         * @return {string} the translated size.
         */
        translateFileSize: function (bytes) {
            const index = Math.floor(Math.log(bytes) / Math.log(1024));
            const unit = ['Bytes', 'Kb', 'Mb', 'Gb', 'Tb'][index];
            const text = (bytes / Math.pow(1024, index)).toFixed(2) * 1;
            return text + ' ' + unit;
        }
    });

    $.extend($.validator.messages, {
        /*
         * required (all)
         */
        requiredFallback: 'This field is required.',
        requiredLabel: 'The field \"{0}\" is required.',
        required: function (_parameters, element) {
            // error message?
            const error = $(element).data('error');
            if (error) {
                return error;
            }
            return $.validator.formatLabel(element, $.validator.messages.requiredLabel, $.validator.messages.requiredFallback);
        },

        /*
         * email
         */
        emailFallback: 'Please provide a valid email address.',
        emailLabel: 'The field \"{0}\" must contain a valid email address.',
        email: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.emailLabel, $.validator.messages.emailFallback);
        },

        /*
         * url
         */
        urlFallback: 'Please provide a valid URL address.',
        urlLabel: 'The field \"{0}\" must contain a valid URL address.',
        url: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.urlLabel, $.validator.messages.urlFallback);
        },

        /*
         * date
         */
        dateFallback: 'Please provide a valid date.',
        dateLabel: 'The field \"{0}\" must contain a valid date.',
        date: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.dateLabel, $.validator.messages.dateFallback);
        },

        /*
         * accept (file type)
         */
        acceptFallback: 'This field must contain a valid file type.',
        acceptLabel: 'The field \"{0}\" must contain a valid file type.',
        accept: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.acceptLabel, $.validator.messages.acceptFallback);
        },

        /*
         * password
         */
        passwordLevels: ['Très faible', 'Faible', 'Moyenne', 'Forte', 'Très forte'],
        passwordFallback: 'This field must have a value \"{0}\" (current value : \"{2}\").',
        passwordLabel: 'The field \"{0}\" must have a value \"{1}\" (current value : \"{2}\").',
        password: function (parameter, element) {
            let current = $(element).findPasswordScore();
            current = $.validator.messages.passwordLevels[current];
            const level = $.validator.messages.passwordLevels[parameter];
            return $.validator.formatLabel(element, $.validator.messages.passwordLabel, $.validator.messages.passwordFallback, level, current);
        },

        /*
         * notUsername
         */
        notUsernameFallback: 'This field cannot contain the username.',
        notUsernameLabel: 'The field \"{0}\" cannot contain username.',
        notUsername: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.notUsernameLabel, $.validator.messages.notUsernameFallback);
        },

        /*
         * notEmail
         */
        notEmailFallback: 'This field cannot be an email address.',
        notEmailLabel: 'The field \"{0}\" cannot be an email address.',
        notEmail: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.notEmailLabel, $.validator.messages.notEmailFallback);
        },

        /*
         * lowercase
         */
        lowercaseFallback: 'This field must contain a lowercase character.',
        lowercaseLabel: 'The field \"{0}\" must contain a lowercase character.',
        lowercase: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.lowercaseLabel, $.validator.messages.lowercaseFallback);
        },

        /*
         * uppercase
         */
        uppercaseFallback: 'This field must contain an uppercase character.',
        uppercaseLabel: 'The field \"{0}\" must contain an uppercase character.',
        uppercase: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.uppercaseLabel, $.validator.messages.uppercaseFallback);
        },

        /*
         * mixedcase
         */
        mixedcaseFallback: 'This field must contain a lowercase and uppercase character.',
        mixedcaseLabel: 'The field \"{0}\" must contain a lowercase and uppercase character.',
        mixedcase: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.mixedcaseLabel, $.validator.messages.mixedcaseFallback);
        },

        /*
         * digit
         */
        digitFallback: 'This field must contain a number.',
        digitLabel: 'The field \"{0}\" must contain a number.',
        digit: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.digitLabel, $.validator.messages.digitFallback);
        },

        /*
         * specialchar
         */
        specialCharFallback: 'This field must contain a special character.',
        specialCharLabel: 'The field \"{0}\" must contain a special character.',
        specialchar: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.specialCharLabel, $.validator.messages.specialCharFallback);
        },

        /*
         * letter
         */
        letterFallback: 'This field must contain an alphabetical character.',
        letterLabel: 'The field \"{0}\" must contain an alphabetical character.',
        letter: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.letterLabel, $.validator.messages.letterFallback);
        },

        /*
         * maximumfiles (file type)
         */
        maxfilesFallback: 'Le nombre de fichiers ne doit pas être supérieur à {0}.',
        maxfilesLabelSingle: 'The field \"{0}\" ne doit contenir qu\'un fichier.',
        maxfilesLabelPlural: 'The field \"{0}\" ne doit pas contenir plus de {1} fichiers.',
        maxfiles: function (count, element) {
            if ($.parseInt(count) > 1) {
                return $.validator.formatLabel(element, $.validator.messages.maxfilesLabelPlural, $.validator.messages.maxfilesFallback, count);
            } else {
                return $.validator.formatLabel(element, $.validator.messages.maxfilesLabelSingle, $.validator.messages.maxfilesFallback, count);
            }
        },

        /*
         * maxsize (file type)
         */
        maxsizeMessage: 'La taille de chaque fichier ne doit pas dépasser {0}.',
        maxsizeFileName: 'Le fichier \"{0}\" dépasse la taille maximale de {1}.',
        maxsize: function (bytes, element) {
            // find file name
            let fileName = false;
            for (let i = 0; i < element.files.length; i++) {
                if (element.files[i].size > bytes) {
                    fileName = element.files[i].name;
                    break;
                }
            }
            const size = $.validator.translateFileSize(bytes);
            if (fileName) {
                return $.validator.format($.validator.messages.maxsizeFileName, fileName, size);
            }
            return $.validator.format($.validator.messages.maxsizeMessage, size);
        },

        /*
         * maxsizetotal (file type)
         */
        maxsizetotalMessage: 'La taille totale de tous les fichiers ne doit pas dépasser {0}.',
        maxsizetotal: function (bytes) {
            const size = $.validator.translateFileSize(bytes);
            return $.validator.format($.validator.messages.maxsizetotalMessage, size);
        },

        /*
         * notEqualTo
         */
        notEqualToFallback: 'Veuillez fournir une valeur différente, les valeurs ne doivent pas être identiques.',
        notEqualToLabel: 'The field \"{0}\" doit être différent.',
        notEqualToBoth: 'The field \"{0}\" doit être différent du champ \"{1}\".',
        notEqualTo: function (parameters, element) {
            const target = $(element).getLabelText();
            const source = $(parameters).getLabelText();
            if (target && source) {
                return $.validator.format($.validator.messages.notEqualToBoth, target, source);
            } else if (target) {
                return $.validator.format($.validator.messages.notEqualToLabel, target);
            } else {
                return $.validator.messages.notEqualToFallback;
            }
        },

        /*
         * greaterThan
         */
        greaterThanFallback: 'Veuillez fournir une valeur supérieure.',
        greaterThanLabel: 'The field \"{0}\" doit avoir une valeur supérieure.',
        greaterThanBoth: 'The field \"{0}\" doit avoir une valeur supérieure au champ \"{1}\".',
        greaterThan: function (parameters, element) {
            const target = $(element).getLabelText();
            const source = $(parameters).getLabelText() || parameters;
            if (target && source) {
                return $.validator.format($.validator.messages.greaterThanBoth, target, source);
            } else if (target) {
                return $.validator.format($.validator.messages.greaterThanLabel, target);
            } else {
                return $.validator.messages.greaterThanFallback;
            }
        },

        /*
         * greaterThanValue
         */
        greaterThanValueFallback: 'Veuillez fournir une valeur supérieure à {0}.',
        greaterThanValueLabel: 'The field \"{0}\" doit avoir une valeur supérieure à {1}.',
        greaterThanValue: function (parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.greaterThanValueLabel, $.validator.messages.greaterThanValueFallback, parameters);
        },

        /*
         * greaterThanEqual
         */
        greaterThanEqualFallback: 'Veuillez fournir une valeur égale ou supérieure.',
        greaterThanEqualLabel: 'The field \"{0}\" doit avoir une valeur égale ou supérieure.',
        greaterThanEqualBoth: 'The field \"{0}\" doit avoir une valeur égale ou supérieure au champ \"{1}\".',
        greaterThanEqual: function (parameters, element) {
            const target = $(element).getLabelText();
            const source = $(parameters).getLabelText();
            if (target && source) {
                return $.validator.format($.validator.messages.greaterThanEqualBoth, target, source);
            } else if (target) {
                return $.validator.format($.validator.messages.greaterThanEqualLabel, target);
            } else {
                return $.validator.messages.greaterThanEqualFallback;
            }
        },

        /*
         * greaterThanEqualValue
         */
        greaterThanEqualValueFallback: 'Veuillez fournir une valeur égale ou supérieure à {0}.',
        greaterThanEqualValueLabel: 'The field \"{0}\" doit avoir une valeur égale ou supérieure à {1}.',
        greaterThanEqualValue: function (parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.greaterThanEqualValueLabel, $.validator.messages.greaterThanEqualValueFallback, parameters);
        },

        /*
         * lessThan
         */
        lessThanFallback: 'Veuillez fournir une valeur inférieure.',
        lessThanLabel: 'The field \"{0}\" doit avoir une valeur inférieure au champ \"{1}\".',
        lessThanBoth: 'The field \"{0}\" doit avoir une valeur inférieure au champ \"{1}\".',
        lessThan: function (parameters, element) {
            const target = $(element).getLabelText();
            const source = $(parameters).getLabelText();
            if (target && source) {
                return $.validator.format($.validator.messages.lessThanBoth, target, source);
            } else if (target) {
                return $.validator.format($.validator.messages.lessThanLabel, target);
            } else {
                return $.validator.messages.lessThanFallback;
            }
        },

        /*
         * lessThanValue
         */
        lessThanValueFallback: 'Veuillez fournir une valeur inférieure à {0}.',
        lessThanValueLabel: 'The field \"{0}\" doit avoir une valeur inférieure à {1}.',
        lessThanValue: function (parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.lessThanValueLabel, $.validator.messages.lessThanValueFallback, parameters);
        },

        /*
         * lessThanEqual
         */
        lessThanEqualFallback: 'Veuillez fournir une valeur égale ou inférieure.',
        lessThanEqualLabel: 'The field \"{0}\" doit avoir une valeur égale ou inférieure.',
        lessThanEqualBoth: 'The field \"{0}\" doit avoir une valeur égale ou inférieure au champ \"{1}\".',
        lessThanEqual: function (parameters, element) {
            const target = $(element).getLabelText();
            const source = $(parameters).getLabelText();
            if (target && source) {
                return $.validator.format($.validator.messages.lessThanEqualBoth, target, source);
            } else if (target) {
                return $.validator.format($.validator.messages.lessThanEqualLabel, target);
            } else {
                return $.validator.messages.lessThanEqualFallback;
            }
        },

        /*
         * lessThanEqualValue
         */
        lessThanEqualValueFallback: 'Veuillez fournir une valeur égale ou inférieure à {0}.',
        lessThanEqualValueLabel: 'The field \"{0}\" doit avoir une valeur égale ou inférieure au champ {1}.',
        lessThanEqualValue: function (parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.lessThanEqualValueLabel, $.validator.messages.lessThanEqualValueFallback, parameters);
        },

        /*
         * notEqualToZero
         */
        notEqualToZeroFallback: 'Veuillez fournir une valeur différente de 0.',
        notEqualToZeroLabel: 'The field \"{0}\" doit être différent de 0.',
        notEqualToZero: function (parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.notEqualToZeroLabel, $.validator.messages.notEqualToZeroFallback, parameters);
        },

        /*
         * minlength
         */
        minlengthFallback: 'Veuillez fournir au moins {0} caractères.',
        minlengthLabel: 'The field \"{0}\" doit avoir au moins {1} caractères.',
        minlength: function (parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.minlengthLabel, $.validator.messages.minlengthFallback, parameters);
        },

        /*
         * maxlength
         */
        maxlengthFallback: 'Veuillez fournir au plus {0} caractères.',
        maxlengthLabel: 'The field \"{0}\" doit avoir au plus {0} caractères.',
        maxlength: function (parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.maxlengthLabel, $.validator.messages.maxlengthFallback, parameters);
        },

        /*
         * alphanumeric
         */
        alphanumericFallback: 'Ce champ ne doit contenir que des lettres, nombres, espaces et soulignages.',
        alphanumericLabel: 'The field \"{0}\" ne doit contenir que des lettres, nombres, espaces et soulignages.',
        alphanumeric: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.alphanumericLabel, $.validator.messages.alphanumericFallback);
        },

        /*
         * lettersonly
         */
        lettersonlyFallback: 'Ce champ ne doit contenir que des lettres.',
        lettersonlyLabel: 'The field \"{0}\" ne doit contenir que des lettres.',
        lettersonly: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.lettersonlyLabel, $.validator.messages.lettersonlyFallback);
        },

        /*
         * nowhitespace
         */
        nowhitespaceFallback: 'Ce champ ne doit pas contenir d\'espace.',
        nowhitespaceLabel: 'The field \"{0}\" ne doit pas contenir d\'espace.',
        nowhitespace: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.nowhitespaceLabel, $.validator.messages.nowhitespaceFallback);
        },

        /*
         * zipcodeCh
         */
        zipcodeChFallback: "Ce champ doit contenir un numéro postal valide.",
        zipcodeChLabel: "Le champ \"{0}\" doit contenir un numéro postal valide.",
        zipcodeCH: function (_parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.zipcodeChLabel, $.validator.messages.zipcodeChFallback);
        },

        /*
         * min
         */
        minFallback: 'Veuillez fournir une valeur supérieure ou égale à {0}.',
        minLabel: 'The field \"{0}\" doit avoir une valeur supérieure ou égale à {1}.',
        min: function (parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.minLabel, $.validator.messages.minFallback, parameters);
        },

        /*
         * max
         */
        maxFallback: 'Veuillez fournir une valeur inférieure ou égale à {0}.',
        maxLabel: 'The field \"{0}\" doit avoir une valeur inférieure ou égale à {1}.',
        max: function (parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.maxLabel, $.validator.messages.maxFallback, parameters);
        },

        /*
         * unique
         */
        uniqueFallback: 'La valeur doit être unique.',
        uniqueLabel: 'The field \"{0}\" doit être unique.',
        unique: function (parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.uniqueLabel, $.validator.messages.uniqueFallback, parameters);
        },

        /*
         * step
         */
        stepFallback: 'La valeur doit être un multiple de {0}.',
        stepLabel: 'The field \"{0}\" doit être un multiple de {1}.',
        step: function (parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.stepLabel, $.validator.messages.stepFallback, parameters);
        },

        /*
        * number
        */
        numberFallback: 'La valeur doit être un nombre.',
        numberLabel: 'The field \"{0}\" doit être un nombre.',
        number: function (parameters, element) {
            return $.validator.formatLabel(element, $.validator.messages.numberLabel, $.validator.messages.numberFallback, parameters);
        },
    });
}(jQuery));
