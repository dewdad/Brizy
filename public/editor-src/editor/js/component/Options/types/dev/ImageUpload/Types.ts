import React from "react";
import * as Option from "visual/component/Options/Type";
import { Image } from "./model";
import { WithClassName, WithConfig } from "visual/utils/options/attributes";

export type Value = Image;

export type ImageDataPatch = {
  imageSrc: string;
  imageExtension: string;
  imageWidth: number;
  imageHeight: number;
};

export interface PositionPatch {
  positionX: number;
  positionY: number;
}

export interface Config {
  pointer: boolean;
  edit: boolean;
}

type Partial = ImageDataPatch | PositionPatch;

export type Props = Option.Props<Value, Partial> &
  WithConfig<Config> &
  WithClassName;

export type Component = React.FC<Props> & Option.OptionType<Value>;
