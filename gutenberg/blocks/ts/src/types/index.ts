/**
 * Interface describing SPTV ServiceLocationServiceChannelBlock component option
 */
export interface ServiceChannelBlockOptionComponent {
  slug: string,
  name: string
}

/**
 * Interface describing SPTV ServiceLocationServiceChannelBlock options
 */
export interface ServiceChannelBlockOptions {
  components: ServiceChannelBlockOptionComponent[]
}

/**
 * Interface describing SPTV ServiceBlock component option
 */
export interface ServiceBlockOptionComponent {
  slug: string,
  name: string
}

/**
 * Interface describing SPTV ServiceBlock options
 */
export interface ServiceBlockOptions {
  components: ServiceBlockOptionComponent[]
}

/**
 * Interface describing SPTV options
 */
export interface SptvOptions {
  serviceLocationServiceChannelBlock: ServiceChannelBlockOptions,
  electronicServiceChannelBlock: ServiceChannelBlockOptions,
  webpageServiceChannelBlock: ServiceChannelBlockOptions,
  printableFormServiceChannelBlock: ServiceChannelBlockOptions,
  phoneServiceChannelBlock: ServiceChannelBlockOptions,
  serviceBlock: ServiceBlockOptions
}