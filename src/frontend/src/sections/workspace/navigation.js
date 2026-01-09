import React from 'react';
import { usePathname } from 'next/navigation';
import { useUrl } from 'src/hooks/use-url';
import NextLink from 'next/link';

const navigation = [
  {
    title: 'Workspace Configurations',
    url: '/manage'
  },
  {
    title: 'Users',
    url: '/manage/users'
  },
  {
    title: 'Groups and Permissions',
    url: '/manage/groups'
  },
  {
    title: 'Webhooks',
    url: '/manage/webhooks'
  }
];

export function WorkspaceNavigation() {
  const pathname = usePathname();
  const url = useUrl();

  return (
    <ul className="workspace-nav">
      {navigation.map((item, key) => {
        const path = url.getUrl(item.url);
        const active = pathname === path;

        return (
          <li
            className={`workspace-nav-item ${active ? 'workspace-nav-item-active' : ''}`}
            key={key}
          >
            <NextLink href={path}>
              {item.title}
            </NextLink>
          </li>
        );
      })}
    </ul>
  );
}
