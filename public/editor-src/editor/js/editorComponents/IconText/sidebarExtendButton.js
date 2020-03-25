import {
  toolbarDisabledPadding,
  toolbarDisabledMargin
} from "visual/utils/toolbar";

export function getItems({ device, state }) {
  return [
    {
      id: "settingsTabs",
      type: "tabs",
      disabled: true
    },
    toolbarDisabledPadding({ device, state }),
    toolbarDisabledMargin({ device, state })
  ];
}
