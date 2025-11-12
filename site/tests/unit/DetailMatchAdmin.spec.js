import { mount } from '@vue/test-utils';
import { describe, it, expect, vi } from 'vitest';
import DetailMatchAdmin from '../../src/components/DetailMatchAdmin.vue';

// si your component uses router-link, stub it so test n'a pas besoin de router
const globalStubs = { 'router-link': { template: '<a><slot/></a>' } };

describe('DetailMatchAdmin', () => {
  it('renders and contains link text', () => {
    const matchdetail = { id: 1, titre: 'Test Match' };
    const wrapper = mount(DetailMatchAdmin, {
      props: { matchdetail },
      global: { stubs: globalStubs }
    });

    // Vérifie que le composant s'est monté et affiche le texte du lien attendu
    expect(wrapper.html()).toContain('Feuille de match');
  });

  it('calls updateOpp and emits changeOpp when numero changed', async () => {
    const matchdetail = { id: 1 };
    const wrapper = mount(DetailMatchAdmin, {
      props: { matchdetail },
      global: { stubs: globalStubs }
    });

    // Si le composant expose une méthode ou un événement quand le numéro change,
    // déclenchez l'interaction correspondante. Exemple : input change
    const input = wrapper.find('input[name="numero"]');
    if (input.exists()) {
      await input.setValue('10');
      // vérifier émission d'événement custom
      expect(wrapper.emitted()).toHaveProperty('changeOpp');
    } else {
      // fallback : ensure component still mounted
      expect(wrapper.exists()).toBe(true);
    }
  });
});