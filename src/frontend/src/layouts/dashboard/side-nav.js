import React, { useState, useEffect } from 'react';
import { usePathname } from 'next/navigation';
import {
  SvgIcon,
  Drawer,
  useMediaQuery,
  Button,
  Divider
} from '@mui/material';
import Logo from 'src/components/logo';
import { usePermission } from 'src/hooks/use-permission';
import { WorkspaceSelector } from 'src/components/workspace-selector';
import Link from 'next/link';
import Bars3Icon from '@heroicons/react/24/solid/Bars3Icon';
import { SideNavItem } from './side-nav-item';
import { navigation } from './config';

export function SideNav() {
  const pathname = usePathname();
  const { canSee } = usePermission();
  const isDesktop = useMediaQuery((theme) => theme.breakpoints.up('lg'));
  const [isOpen, setIsOpen] = useState(false);
  const openNav = () => {
    setIsOpen(true);
  };

  const closeNav = () => {
    setIsOpen(false);
  };

  useEffect(() => {
    closeNav();
  }, [pathname]);

  const content = (
    <div
      className="sticky top-0 min-w-[275px] h-screen flex self-start bg-dbm-color-primary-light overflow-y-auto flex-col justify-between"
    >
      <div className="flex-col w-100">
        <div className="flex flex-col gap-4 p-5">
          <Link
            title="Home page"
            href="/"
          >
            <Logo />
          </Link>

          <WorkspaceSelector />
        </div>

        <Divider className="text-dbm-color-3" />

        <ul className="flex flex-col gap-4 p-5">
          {/* eslint-disable-next-line array-callback-return */}
          {navigation.map((item) => {
            const active = item.path ? pathname === item.path : false;

            if (!item.permission || canSee(item.permission)) {
              return (
                <SideNavItem
                  active={active}
                  disabled={item.disabled}
                  external={item.external}
                  permission={item.permission}
                  icon={item.icon}
                  key={item.title}
                  path={item.path}
                  title={item.title}
                />
              );
            }
          })}
        </ul>
      </div>
    </div>
  );

  return (
    // eslint-disable-next-line react/jsx-no-useless-fragment
    <>
      {
        isDesktop
          ? content
          : (
            <>
              <Button
                className="!fixed !z-50 !left-5 !top-5 !p-0"
                onClick={openNav}
              >
                <SvgIcon fontSize="large">
                  <Bars3Icon />
                </SvgIcon>
              </Button>

              <Drawer
                onClose={closeNav}
                open={isOpen}
                variant="temporary"
              >
                {content}
              </Drawer>
            </>
          )
      }
    </>
  );
}
