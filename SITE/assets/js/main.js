const modalTriggers = document.querySelectorAll('[data-modal-target]');
const modals = document.querySelectorAll('.modal');
let lastFocusedElement = null;

function getFocusableElements(container) {
  return Array.from(
    container.querySelectorAll('a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])')
  ).filter((element) => element.offsetParent !== null);
}

function openModal(modalId, trigger = null) {
  const modal = document.getElementById(modalId);

  if (!modal) {
    return;
  }

  lastFocusedElement = trigger || document.activeElement;
  modal.classList.add('is-open');
  modal.setAttribute('aria-hidden', 'false');
  document.body.classList.add('modal-open');

  const focusable = getFocusableElements(modal);
  const firstFocusable = focusable[0] || modal;
  firstFocusable.focus();
}

function closeModal(modal) {
  if (!modal) {
    return;
  }

  modal.classList.remove('is-open');
  modal.setAttribute('aria-hidden', 'true');

  const anyOpen = Array.from(modals).some((item) => item.classList.contains('is-open'));
  if (!anyOpen) {
    document.body.classList.remove('modal-open');
  }

  const mediaModalBody = modal.querySelector('[data-media-modal-body]');
  if (mediaModalBody) {
    mediaModalBody.innerHTML = '';
  }

  if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
    lastFocusedElement.focus();
  }
}

modalTriggers.forEach((trigger) => {
  const modalId = trigger.dataset.modalTarget;

  trigger.addEventListener('click', () => openModal(modalId, trigger));

  trigger.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      openModal(modalId, trigger);
    }
  });
});

modals.forEach((modal) => {
  modal.addEventListener('click', (event) => {
    if (event.target.matches('[data-modal-close]')) {
      closeModal(modal);
    }
  });

  modal.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeModal(modal);
      return;
    }

    if (event.key !== 'Tab') {
      return;
    }

    const focusable = getFocusableElements(modal);
    if (focusable.length === 0) {
      return;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    }

    if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  });
});

document.addEventListener('keydown', (event) => {
  if (event.key !== 'Escape') {
    return;
  }

  const openModalElement = document.querySelector('.modal.is-open');
  closeModal(openModalElement);
});

const filterForms = document.querySelectorAll('[data-live-filter]');

function normalizeText(value) {
  return String(value || '').toLocaleLowerCase('ka-GE').trim();
}

function parseSortDate(value) {
  const text = String(value || '').trim();
  const directDate = Date.parse(text);

  if (!Number.isNaN(directDate)) {
    return directDate;
  }

  const georgianDate = text.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})/);
  if (!georgianDate) {
    return 0;
  }

  const [, day, month, year] = georgianDate;
  return Date.parse(`${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`) || 0;
}

function getSortValue(item, field) {
  if (field === 'date') {
    return parseSortDate(item.dataset.sortDate);
  }

  if (field === 'size') {
    return Number(item.dataset.sortSize || 0);
  }

  return normalizeText(item.dataset[`sort${field.charAt(0).toUpperCase()}${field.slice(1)}`] || item.dataset[field] || item.dataset.title || item.textContent);
}

function compareValues(a, b, direction) {
  if (typeof a === 'number' && typeof b === 'number') {
    return direction === 'desc' ? b - a : a - b;
  }

  const result = String(a).localeCompare(String(b), 'ka-GE');
  return direction === 'desc' ? -result : result;
}

filterForms.forEach((form) => {
  const targetSelector = form.dataset.filterTarget;
  const target = document.querySelector(targetSelector);
  const emptyState = form.parentElement.querySelector('[data-empty-state]');

  if (!target) {
    return;
  }

  const items = Array.from(target.querySelectorAll('.filter-item'));

  function applyFilter() {
    const formData = new FormData(form);
    const search = normalizeText(formData.get('search'));
    const category = normalizeText(formData.get('category'));
    const sort = String(formData.get('sort') || '');
    let visibleCount = 0;

    items.forEach((item) => {
      const text = normalizeText([
        item.dataset.title,
        item.dataset.text,
        item.dataset.category,
        item.textContent,
      ].join(' '));
      const itemCategory = normalizeText(item.dataset.category);
      const matchesSearch = search === '' || text.includes(search);
      const matchesCategory = category === '' || itemCategory === category;
      const isVisible = matchesSearch && matchesCategory;

      item.hidden = !isVisible;
      if (isVisible) {
        visibleCount += 1;
      }
    });

    if (sort.includes('-')) {
      const [field, direction] = sort.split('-');
      const sortedItems = [...items].sort((first, second) => {
        return compareValues(getSortValue(first, field), getSortValue(second, field), direction);
      });

      sortedItems.forEach((item) => target.appendChild(item));
    }

    if (emptyState) {
      emptyState.hidden = visibleCount !== 0;
    }
  }

  form.addEventListener('submit', (event) => event.preventDefault());
  form.addEventListener('input', applyFilter);
  form.addEventListener('change', applyFilter);
  applyFilter();
});

function youtubeEmbedUrl(url) {
  try {
    const parsed = new URL(url);
    let id = '';

    if (parsed.hostname.includes('youtu.be')) {
      id = parsed.pathname.replace('/', '');
    } else if (parsed.searchParams.has('v')) {
      id = parsed.searchParams.get('v');
    } else if (parsed.pathname.includes('/embed/')) {
      id = parsed.pathname.split('/embed/')[1];
    }

    if (!/^[a-zA-Z0-9_-]{6,}$/.test(id || '')) {
      return '';
    }

    return `https://www.youtube.com/embed/${id}`;
  } catch (error) {
    return '';
  }
}

const mediaModalBody = document.querySelector('[data-media-modal-body]');

document.querySelectorAll('[data-lightbox-src]').forEach((button) => {
  button.addEventListener('click', () => {
    if (!mediaModalBody) {
      return;
    }

    const src = button.dataset.lightboxSrc;
    const alt = button.dataset.lightboxAlt || 'სურათი';
    mediaModalBody.innerHTML = '';

    const image = document.createElement('img');
    image.src = src;
    image.alt = alt;
    mediaModalBody.appendChild(image);
    openModal('mediaModal', button);
  });
});

document.querySelectorAll('[data-video-url]').forEach((button) => {
  button.addEventListener('click', () => {
    if (!mediaModalBody) {
      return;
    }

    const embedUrl = youtubeEmbedUrl(button.dataset.videoUrl);
    mediaModalBody.innerHTML = '';

    if (!embedUrl) {
      const message = document.createElement('p');
      message.className = 'modal-lead';
      message.textContent = 'ვიდეოს ბმული არასწორია.';
      mediaModalBody.appendChild(message);
    } else {
      const iframe = document.createElement('iframe');
      iframe.src = embedUrl;
      iframe.title = button.dataset.videoTitle || 'ვიდეო';
      iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
      iframe.allowFullscreen = true;
      mediaModalBody.appendChild(iframe);
    }

    openModal('mediaModal', button);
  });
});
