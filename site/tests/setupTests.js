// Mock lodash.debounce to call synchronously in tests
import { vi } from 'vitest';
vi.mock('lodash', () => ({ debounce: (fn) => fn }));