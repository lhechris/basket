import { mount } from '@vue/test-utils'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import MatchsView from '../../src/views/MatchsView.vue'
import DetailMatch from '../../src/components/DetailMatch.vue'
import { getMatchsAvecSel, displaydate } from '../../src/js/api.js'

import mockMatchs from '../../../backend/tests/data/matchsavecsel.json'

// Mock the API calls
vi.mock('../../src/js/api.js', () => ({
  getMatchsAvecSel: vi.fn(),
  displaydate: vi.fn((date) => 'Formatted ' + date)
}))

describe('MatchsView.vue', () => {

  beforeEach(() => {
    vi.clearAllMocks()
    getMatchsAvecSel.mockResolvedValue(mockMatchs)
  })

  /**
   * Test qu'on monte bien le composant
   * La fonction getMatchsAvecSel doit être appelée
   * 
   */
  it('fetches and displays matches on mount', async () => {
    const wrapper = mount(MatchsView)
    await wrapper.vm.$nextTick()
    
    // Verify API was called
    expect(getMatchsAvecSel).toHaveBeenCalled()
    
    // Check if dates are displayed and formatted
    const dates = wrapper.findAll('span')
    expect(dates[0].text()).toBe('Formatted 2025-09-20')
    
    // Verify DetailMatch components are rendered
    const detailMatches = wrapper.findAllComponents(DetailMatch)
    expect(detailMatches).toHaveLength(1) // Total number of matches
  })


  /**
   * Test que la premiere page est fonction de la date courante
   * Ca doit etre la date juste après
   */
  it('sets page to first future match', async () => {
    // Mock current date to be before first match
    vi.useFakeTimers()
    vi.setSystemTime(new Date('2025-09-30'))
    
    const wrapper = mount(MatchsView)
    await wrapper.vm.$nextTick()
    
    expect(wrapper.vm.page).toBe(3)
    
    // Change date to between matches (on selectionne la meme date)
    vi.setSystemTime(new Date('2025-10-05'))
    await wrapper.vm.refreshMatch()
    await wrapper.vm.$nextTick()
    
    expect(wrapper.vm.page).toBe(3)
    
    vi.useRealTimers()
  })

  /**
   * Verifie que les proprietes envoyée sont ok
   */
  it('passes correct props to DetailMatch components', async () => {
    vi.setSystemTime(new Date('2025-09-19'))

    const wrapper = mount(MatchsView)
    await wrapper.vm.$nextTick()
    
    const detailMatches = wrapper.findAllComponents(DetailMatch)
    
    expect(detailMatches[0].props('matchdetail')).toEqual({ id: 2, 
                                                            titre: 'match1', 
                                                            equipe: 1,
                                                            jour: "2025-09-20",
                                                            titre: "match1",
                                                            score: "0/0",
                                                            otm: "picsou",
                                                            collation: "donald",
                                                            maillots: "nobody",
                                                            adresse: "ici ou la bas",
                                                            horaire: "12h00",
                                                            rendezvous: "11h00",
                                                            oppositions:null,
                                                            selections:[]
                                                          })
  })


  /**
   * Test avec un tableau vide 
   * ie getMatchsAvecSel retourne []
   */
  it('handles empty matches array', async () => {
    getMatchsAvecSel.mockResolvedValue([])
    
    const wrapper = mount(MatchsView)
    await wrapper.vm.$nextTick()
    
    const detailMatches = wrapper.findAllComponents(DetailMatch)
    expect(detailMatches).toHaveLength(0)
    expect(wrapper.vm.page).toBe(1)
  })
})