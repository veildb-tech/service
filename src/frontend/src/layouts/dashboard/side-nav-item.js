import React from 'react';
import Link from 'next/link';
import PropTypes from 'prop-types';

export function SideNavItem(props) {
  const {
    active = false, icon, path, title
  } = props;

  return (
    <li>
      <Link
        title="Home page"
        href={path}
        className={`
          flex
          items-center
          gap-2
          text-sm
          leading-4
          font-medium
          hover:text-dbm-color-white
          ${active ? 'text-dbm-color-white' : 'text-dbm-color-11'}
        `}
      >
        {icon}
        <span>{title}</span>
      </Link>
    </li>
  );
}

SideNavItem.propTypes = {
  active: PropTypes.bool,
  // eslint-disable-next-line react/no-unused-prop-types
  disabled: PropTypes.bool,
  // eslint-disable-next-line react/no-unused-prop-types
  external: PropTypes.bool,
  icon: PropTypes.node,
  path: PropTypes.string,
  title: PropTypes.string.isRequired,
};
