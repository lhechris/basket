import { describe, it, expect, beforeAll } from 'vitest';
import moment from 'moment';
import { displaydate, displaydatemin, isjourdepasse, getFirstDateAfterNow } from '../../src/js/api.js';

describe('dateUtils', () => {
    beforeAll(() => {
        moment.locale('fr');
    });

    it('displaydate() doit retourner la date formatée en français', () => {
        const date = '2025-11-21';
        const result = displaydate(date);
        expect(result).toMatch(/vendredi 21 novembre/);
    });

    it('displaydatemin() doit retourner la date au format DD/MM', () => {
        const date = '2025-11-21';
        const result = displaydatemin(date);
        expect(result).toBe('21/11');
    });

    it('isjourdepasse() doit retourner true pour une date passée', () => {
        const pastDate = moment().subtract(1, 'day').format('YYYY-MM-DD');
        expect(isjourdepasse(pastDate)).toBe(true);
    });

    it('isjourdepasse() doit retourner false pour une date future', () => {
        const futureDate = moment().add(1, 'day').format('YYYY-MM-DD');
        expect(isjourdepasse(futureDate)).toBe(false);
    });

    it('isjourdepasse() doit retourner false pour la date du jour', () => {
        const todayDate = moment().format('YYYY-MM-DD');
        expect(isjourdepasse(todayDate)).toBe(false);
    });

    describe('getFirstDateAfterNow()', () => {
        it('retourne l’index de la première date après maintenant', () => {
            const liste = [
                { jour: moment().subtract(2, 'days').format('YYYY-MM-DD') },
                { jour: moment().add(1, 'days').format('YYYY-MM-DD') },
                { jour: moment().add(3, 'days').format('YYYY-MM-DD') }
            ];
            const result = getFirstDateAfterNow(liste, false);
            expect(result).toBe(1);
        });

        it('retourne 0 si aucune date n’est après maintenant', () => {
            const liste = [
                { jour: moment().subtract(2, 'days').format('YYYY-MM-DD') },
                { jour: moment().subtract(1, 'days').format('YYYY-MM-DD') }
            ];
            const result = getFirstDateAfterNow(liste, false);
            expect(result).toBe(0);
        });

        it('retourne l’index si stricte=true et date égale à aujourd’hui', () => {
            const liste = [
                { jour: moment().subtract(2, 'days').format('YYYY-MM-DD') },
                { jour: moment().subtract(1, 'days').format('YYYY-MM-DD') },
                { jour: moment().format('YYYY-MM-DD') },
                { jour: moment().add(1, 'days').format('YYYY-MM-DD') }
            ];
            const result = getFirstDateAfterNow(liste, true);
            expect(result).toBe(3);
        });

        it('retourne l’index si stricte=false et date égale à aujourd’hui', () => {
            const liste = [
                { jour: moment().subtract(2, 'days').format('YYYY-MM-DD') },
                { jour: moment().subtract(1, 'days').format('YYYY-MM-DD') },
                { jour: moment().format('YYYY-MM-DD') },
                { jour: moment().add(1, 'days').format('YYYY-MM-DD') }
            ];
            const result = getFirstDateAfterNow(liste, false);
            expect(result).toBe(2);
        });


    });
});
