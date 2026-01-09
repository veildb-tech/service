import React from 'react';
import { withAuthGuard } from 'src/hocs/with-auth-guard';
import { ConfigProvider } from 'src/contexts/config-context';
import { SideNav } from './side-nav';
import { TopNav } from './top-nav';

export const Layout = withAuthGuard((props) => {
  const {
    children
  } = props;

  return (
    <ConfigProvider>
      <div className="flex flex-auto page-root">
        <SideNav />

        <div className="flex flex-col w-full gradient">
          <TopNav />

          {children}
        </div>
      </div>
    </ConfigProvider>
  );
});
