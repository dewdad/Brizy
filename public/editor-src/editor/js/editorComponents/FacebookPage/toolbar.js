import { t } from "visual/utils/i18n";
import { hexToRgba } from "visual/utils/color";
import { getOptionColorHexByPalette } from "visual/utils/options";
import { defaultValueValue, defaultValueKey } from "visual/utils/onChange";
import { NORMAL, HOVER } from "visual/utils/stateMode";

export function getItems({ v, device, state }) {
  const dvk = key => defaultValueKey({ key, device });
  const dvv = key => defaultValueValue({ v, key, device, state });

  const { hex: borderColorHex } = getOptionColorHexByPalette(
    dvv("borderColorHex"),
    dvv("borderColorPalette")
  );

  return [
    {
      id: dvk("toolbarCurrentElement"),
      type: "popover",
      devices: "desktop",
      icon: "nc-facebook",
      title: t("Page"),
      position: 70,
      options: [
        {
          id: "tabsCurrentElement",
          type: "tabs",
          tabs: [
            {
              id: "tabCurrentElement",
              label: t("Page"),
              options: [
                {
                  id: "href",
                  label: t("Link"),
                  type: "inputText-dev",
                  devices: "desktop",
                  placeholder: "https://facebook.com/brizy.io"
                },
                {
                  id: "pageTabs",
                  label: t("Tabs"),
                  type: "select-dev",
                  devices: "desktop",
                  choices: [
                    {
                      title: t("Timeline"),
                      value: "timeline"
                    },
                    {
                      title: t("Events"),
                      value: "events"
                    },
                    {
                      title: t("Messages"),
                      value: "messages"
                    }
                  ]
                },
                {
                  id: "height",
                  label: t("Height"),
                  type: "slider-dev",
                  devices: "desktop",
                  config: {
                    min: 70,
                    max: 800,
                    units: [{ value: "px", title: "px" }],
                    debounceUpdate: true
                  }
                }
              ]
            },
            {
              id: "tabAdvanced",
              label: t("Advanced"),
              type: "tabs",
              options: [
                {
                  id: "smallHeader",
                  label: t("Use Small Header"),
                  type: "switch-dev",
                  devices: "desktop"
                },
                {
                  id: "hideCover",
                  label: t("Hide Cover Photo"),
                  type: "switch-dev",
                  devices: "desktop"
                },
                {
                  id: "showFacepile",
                  label: t("Show Friend's Faces"),
                  type: "switch-dev",
                  devices: "desktop"
                }
              ]
            }
          ]
        }
      ]
    },
    {
      id: dvk("toolbarColor"),
      type: "popover",
      size: "auto",
      title: t("Colors"),
      roles: ["admin"],
      position: 80,
      devices: "desktop",
      icon: {
        style: {
          backgroundColor: hexToRgba(borderColorHex, v.borderColorOpacity)
        }
      },
      options: [
        {
          id: "tabsColor",
          type: "tabs",
          hideHandlesWhenOne: false,
          tabs: [
            {
              id: "tabBorder",
              label: t("Border"),
              options: [
                {
                  id: "border",
                  type: "border-dev",
                  states: [NORMAL, HOVER]
                }
              ]
            },
            {
              id: "tabBoxShadow",
              label: t("Shadow"),
              options: [
                {
                  id: "boxShadow",
                  type: "boxShadow-dev",
                  states: [NORMAL, HOVER]
                }
              ]
            }
          ]
        }
      ]
    },
    {
      id: dvk("advancedSettings"),
      type: "advancedSettings",
      sidebarLabel: t("More Settings"),
      roles: ["admin"],
      position: 110,
      icon: "nc-cog"
    },
    {
      id: "horizontalAlign",
      type: "toggle-dev",
      disabled: true
    }
  ];
}
