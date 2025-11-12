import { mount } from '@vue/test-utils'
import { describe, it, expect, vi, beforeEach } from 'vitest'
import HomeView from '../../src/views/HomeView.vue'
import Presence from '../../src/components/Presence.vue'
import { getDisponibilites, setDisponibilite } from '../../src/js/api.js'

import mockDisponibilites from '../../../backend/tests/data/dispo.json'

// Mock the API calls
vi.mock('../../src/js/api.js', () => ({
  getDisponibilites: vi.fn(),
  setDisponibilite: vi.fn(),
  displaydate: vi.fn((date) => 'Formated'+date)
}))

describe('HomeView.vue', () => {

  beforeEach(() => {
    // Reset mock implementations before each test
    vi.clearAllMocks()
    getDisponibilites.mockResolvedValue(mockDisponibilites)
    setDisponibilite.mockResolvedValue(mockDisponibilites)
  })

  /**
   * Test qu'au montage du composants, la fonction getDisponibilite 
   * est appelée et qu'on affiche un seul composant  
   */
  it('renders disponibilites list correctly', async () => {
    const wrapper = mount(HomeView)
    
    // Wait for component to settle / next tick
    await wrapper.vm.$nextTick()
    
    // Verify API was called
    expect(getDisponibilites).toHaveBeenCalled()
    
    // Check if users are rendered
    const userElements = wrapper.findAll('[vtprenom]')
    expect(userElements).toHaveLength(4) // 4 users parce qu'une seule date affichée
    expect(userElements[0].text()).toBe('daisy')
    expect(userElements[1].text()).toBe('fifi')
    expect(userElements[2].text()).toBe('loulou')
    expect(userElements[3].text()).toBe('riri')
  })
  

  /**
   * Test que l'evenement provenant du composant Presence déclenche la fonction 
   * setDisponibilite() avec les bon paramètres
   */
  it('updates disponibilite when Presence component emits update', async () => {
    const wrapper = mount(HomeView)
    await wrapper.vm.$nextTick()

    // Find first Presence component and trigger update
    const presenceComponents = wrapper.findAllComponents(Presence)
    // emit from the child component instance
    presenceComponents[0].vm.$emit('onUpdate', 2)
    await wrapper.vm.$nextTick()

    // Verify setDisponibilite was called with correct parameters
    expect(setDisponibilite).toHaveBeenCalledWith(4, '2025-09-01', 2)
  })


  /**
   * On positionne la date courante pour verifier que la 
   * page selectionnée est la date juste après
   */
  it('sets correct initial page based on current date', async () => {
    // Mock current date to be before first disponibilite
    vi.useFakeTimers()
    vi.setSystemTime(new Date('2025-09-05'))

    const wrapper = mount(HomeView)
    await wrapper.vm.$nextTick()

    // Page should be 1 since first date is next
    expect(wrapper.vm.page).toBe(2)

    // Reset timer
    vi.useRealTimers()
  })

  /**
   * Verifie que la date affichée est celle qui est retournée
   * par la fonction displayDate()
   */
  it('displays dates in correct format', async () => {
    const wrapper = mount(HomeView)
    await wrapper.vm.$nextTick()

    const dateElements = wrapper.findAll('.date')
    expect(dateElements).toHaveLength(1)
    expect(dateElements[0].text()).toBe('Formated2025-09-01')
  })
})