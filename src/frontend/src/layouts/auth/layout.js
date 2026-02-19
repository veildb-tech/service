import React from 'react';
import PropTypes from 'prop-types';
import NextLink from 'next/link';
import Logo from 'src/components/logo';

export function Layout(props) {
  const { children } = props;

  return (
    <div className="flex flex-auto">
      <div className="flex flex-col w-full">
        <main className="h-full p-0 max-w-full">
          <div className="flex h-full">
            <NextLink
              className="lg:hidden absolute p-4 top-0 left-0"
              href="/"
            >
              <img
                alt="Main Logo"
                src="/assets/main-logo.png"
              />
            </NextLink>

            {children}

            <div className="bg-[url('/assets/auth-page-background.svg')] min-w-[626px] h-full bg-cover hidden lg:flex items-center justify-center">
              <div className="flex flex-col -mt-[40%]">
                <div className="mb-10 ">
                  <Logo loginPage />
                </div>

                <div className="text-dbm-color-white mb-4 font-jakarta font-bold text-lg">Welcome to</div>
                <div className="text-dbm-color-white font-jakarta font-bold text-6-7lg">VeilDB</div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>

  );
}

Layout.prototypes = {
  children: PropTypes.node,
};
