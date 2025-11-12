import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import Presence from '../../src/components/Presence.vue'

describe('Presence.vue', () => {
  it('renders three buttons with correct initial classes', () => {
    const wrapper = mount(Presence, {
      props: {
        sel: 1
      }
    })

    const buttons = wrapper.findAll('button')
    expect(buttons).toHaveLength(3)
    
    // Check présente button is selected
    expect(buttons[0].classes()).toContain('presente')
    expect(buttons[0].classes()).not.toContain('unselected')
    
    // Check other buttons are unselected
    expect(buttons[1].classes()).toContain('unselected')
    expect(buttons[2].classes()).toContain('unselected')
  })

  it('emits onUpdate event when clicking different status', async () => {
    const wrapper = mount(Presence, {
      props: {
        sel: 1
      }
    })

    // Click absente button (status 2)
    await wrapper.findAll('button')[1].trigger('click')
    
    // Verify emit
    expect(wrapper.emitted()).toHaveProperty('onUpdate')
    expect(wrapper.emitted('onUpdate')[0]).toEqual([2])
    
    // Verify button states updated
    expect(wrapper.findAll('button')[0].classes()).toContain('unselected')
    expect(wrapper.findAll('button')[1].classes()).not.toContain('unselected')
  })

  it('does not emit when clicking current status', async () => {
    const wrapper = mount(Presence, {
      props: {
        sel: 1
      }
    })

    // Click présente button (already selected)
    await wrapper.findAll('button')[0].trigger('click')
    
    // Verify no emit occurred
    expect(wrapper.emitted('onUpdate')).toBeFalsy()
  })

  it('handles peut-être status correctly', async () => {
    const wrapper = mount(Presence, {
      props: {
        sel: 0
      }
    })

    const buttons = wrapper.findAll('button')
    
    // Check peut-être button is selected
    expect(buttons[2].classes()).not.toContain('unselected')
    expect(buttons[2].text()).toBe('Peut être')
    
    // Other buttons should be unselected
    expect(buttons[0].classes()).toContain('unselected')
    expect(buttons[1].classes()).toContain('unselected')
  })
})